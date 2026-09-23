<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\AttendanceSetting;
use App\Models\User;
use App\Services\AttendanceValidationService;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AttendanceRadiusTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['app.key' => str_repeat('a', 32), 'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        Schema::create('kelas', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kelas');
        });
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->integer('kelas_id')->nullable();
            $table->integer('role');
        });
        foreach ([101, 102, 103] as $id) DB::table('kelas')->insert(['id' => $id, 'nama_kelas' => 'Sekolah Sintetis ' . $id]);
        (require database_path('migrations/2026_04_23_072128_create_attendance_feature_tables.php'))->up();
        (require database_path('migrations/2026_04_23_120000_add_daily_check_columns_to_attendances_table.php'))->up();
        (require database_path('migrations/2026_09_16_000000_add_radius_geofence_to_attendance_settings.php'))->up();
        $this->travelTo(Carbon::parse('2026-09-16 07:00:00', 'Asia/Jakarta'));
    }

    private function actor(int $id = 1, ?int $school = 101, int $role = 2): void
    {
        $this->actingAs((new User())->forceFill(['id' => $id, 'kelas_id' => $school, 'role' => $role]));
    }

    private function school(int $id = 101, float $radius = 100): AttendanceSetting
    {
        $setting = app(AttendanceValidationService::class)->settingForKelas($id);
        $setting->update(['geofence_mode' => 'radius', 'center_latitude' => -7, 'center_longitude' => 110,
            'radius_meters' => $radius, 'max_gps_accuracy' => 30]);
        return $setting;
    }

    private function payload(float $meters, float $bearing = 0, float $accuracy = 5): array
    {
        // Independent spherical destination formula, using four sides of a synthetic school.
        $angularDistance = $meters / 6371000;
        $lat = deg2rad(-7);
        $theta = deg2rad($bearing);
        $destinationLat = asin(sin($lat) * cos($angularDistance) + cos($lat) * sin($angularDistance) * cos($theta));
        $destinationLng = deg2rad(110) + atan2(sin($theta) * sin($angularDistance) * cos($lat),
            cos($angularDistance) - sin($lat) * sin($destinationLat));
        return ['check_type' => 'datang', 'latitude' => rad2deg($destinationLat),
            'longitude' => rad2deg($destinationLng), 'gps_accuracy' => $accuracy];
    }

    private function settingsPayload(float $radius): array
    {
        return ['geofence_mode' => 'radius', 'center_latitude' => -7, 'center_longitude' => 110,
            'radius_meters' => $radius, 'max_gps_accuracy' => 30, 'check_in_time' => '07:00',
            'check_out_time' => '14:00', 'late_tolerance_minutes' => 10,
            'enable_check_in' => 1, 'enable_check_out' => 1, 'enable_permission' => 1, 'enable_fake_gps_detection' => 1,
            'geofence_polygon' => '[]'];
    }

    public function test_radius_accepts_ten_fifty_ninety_nine_and_boundary_on_all_sides_for_arrival_and_departure(): void
    {
        $this->school();
        $id = 1;
        foreach ([10, 50, 99, 100] as $meters) {
            foreach ([0, 90, 180, 270] as $bearing) {
                $this->actor($id++);
                $this->travelTo(Carbon::parse('2026-09-16 07:00:00'));
                $this->postJson(route('mobile.role2.presensi.store'), $this->payload($meters, $bearing))
                    ->assertOk()->assertJson(['success' => true]);
                $this->travelTo(Carbon::parse('2026-09-16 14:00:00'));
                $this->postJson(route('mobile.role2.presensi.store'), array_merge($this->payload($meters, $bearing), ['check_type' => 'pulang']))
                    ->assertOk()->assertJson(['success' => true]);
            }
        }
        $this->assertDatabaseCount('attendances', 16);
        $this->assertSame(16, Attendance::whereNotNull('check_in_at')->whereNotNull('check_out_at')->count());
    }

    public function test_one_hundred_one_meters_is_outside_even_when_accuracy_is_thirty_meters(): void
    {
        $this->school();
        $this->actor();
        $response = $this->postJson(route('mobile.role2.presensi.store'), $this->payload(101, 90, 30))
            ->assertStatus(422)->assertJson(['rejection_code' => 'outside_geofence']);
        $this->assertStringContainsString('101 meter', $response->json('message'));
        $this->assertStringContainsString('100 meter', $response->json('message'));
        $this->assertDatabaseHas('attendances', ['status' => 'ditolak', 'is_inside_geofence' => false]);
    }

    public function test_admin_radius_change_is_used_immediately_and_stale_context_does_not_write_a_rejection(): void
    {
        $this->school(101, 50);
        $this->actor();
        $old = $this->getJson(route('mobile.role2.presensi.location-context'))->assertOk()->json('location_context');
        $this->actor(9, 101, 3);
        $this->post(route('presensi.settings.update'), $this->settingsPayload(150))->assertSessionHasNoErrors()->assertRedirect();
        $this->assertDatabaseHas('attendance_settings', ['kelas_id' => 101, 'radius_meters' => 150]);
        $this->actor();
        $this->postJson(route('mobile.role2.presensi.store'), $this->payload(100) + ['location_settings_version' => $old['version']])
            ->assertStatus(409)->assertJson(['rejection_code' => 'location_settings_changed']);
        $this->assertDatabaseCount('attendances', 0);
        $fresh = $this->getJson(route('mobile.role2.presensi.location-context'))->assertOk()->json('location_context');
        $this->assertNotSame($old['version'], $fresh['version']);
        $this->assertEquals(150, $fresh['geofence']['radius_meters']);
        $this->postJson(route('mobile.role2.presensi.store'), $this->payload(100) + ['location_settings_version' => $fresh['version']])->assertOk();
    }

    public function test_schools_keep_separate_radii_and_request_fields_cannot_override_the_school_area(): void
    {
        $this->school(101, 50);
        $this->school(102, 200);
        $this->actor();
        $this->postJson(route('mobile.role2.presensi.store'), $this->payload(100) + ['kelas_id' => 102, 'radius_meters' => 5000,
            'center_latitude' => -7, 'center_longitude' => 110])->assertStatus(422)->assertJson(['rejection_code' => 'outside_geofence']);
        $this->actor(2, 102);
        $this->postJson(route('mobile.role2.presensi.store'), $this->payload(100))->assertOk();
        $this->actor(3, 101, 3);
        $this->post(route('presensi.settings.update'), $this->settingsPayload(75) + ['kelas_id' => 102])->assertRedirect();
        $this->assertDatabaseHas('attendance_settings', ['kelas_id' => 101, 'radius_meters' => 75]);
        $this->assertDatabaseHas('attendance_settings', ['kelas_id' => 102, 'radius_meters' => 200]);
    }

    public function test_inaccurate_inside_location_reports_accuracy_instead_of_outside_area(): void
    {
        $this->school();
        $this->actor();
        $this->postJson(route('mobile.role2.presensi.store'), $this->payload(50, 0, 31))
            ->assertStatus(422)->assertJson(['rejection_code' => 'invalid_accuracy']);
        $this->postJson(route('mobile.role2.presensi.store'), $this->payload(50, 0, 30))->assertOk();
    }

    public function test_missing_center_does_not_fall_back_to_a_polygon_or_produce_a_server_error(): void
    {
        $setting = $this->school();
        $setting->update(['center_latitude' => null, 'geofence_polygon' => [[-8, 109], [-8, 111], [-6, 111], [-6, 109]]]);
        $this->actor();
        $this->postJson(route('mobile.role2.presensi.store'), $this->payload(10))
            ->assertStatus(422)->assertJson(['rejection_code' => 'geofence_not_configured']);
        $this->actor(9, 101, 3);
        $this->postJson(route('presensi.settings.update'), array_merge($this->settingsPayload(100), ['center_latitude' => null]))
            ->assertStatus(422)->assertJsonValidationErrors('center_latitude');
    }

    public function test_invalid_coordinates_radius_and_school_are_rejected(): void
    {
        $this->school();
        $this->actor(9, 101, 3);
        foreach ([['center_latitude' => 110], ['center_longitude' => 181], ['radius_meters' => 0], ['radius_meters' => -1]] as $invalid) {
            $this->postJson(route('presensi.settings.update'), array_merge($this->settingsPayload(100), $invalid))->assertStatus(422);
        }
        $this->actor(9, null, 3);
        $this->postJson(route('presensi.settings.update'), $this->settingsPayload(100))->assertForbidden();
        $this->actor(1, null);
        $this->postJson(route('mobile.role2.presensi.store'), $this->payload(10))->assertStatus(422);
    }

    public function test_fake_gps_selfie_and_schedule_requirements_still_apply_in_radius_mode(): void
    {
        $setting = $this->school();
        $this->actor();
        $this->postJson(route('mobile.role2.presensi.store'), $this->payload(10) + ['is_mock_location' => true])
            ->assertStatus(422)->assertJson(['rejection_code' => 'fake_gps']);
        $setting->update(['require_selfie' => true]);
        $this->postJson(route('mobile.role2.presensi.store'), $this->payload(10))->assertStatus(422);
        $setting->update(['require_selfie' => false]);
        $this->travelTo(Carbon::parse('2026-09-16 07:11:00'));
        $this->postJson(route('mobile.role2.presensi.store'), $this->payload(10))->assertOk()->assertJson(['status' => 'terlambat']);
        $out = array_merge($this->payload(10), ['check_type' => 'pulang']);
        $this->postJson(route('mobile.role2.presensi.store'), $out)->assertStatus(422)->assertJson(['rejection_code' => 'early_checkout_reason_required']);
        $this->postJson(route('mobile.role2.presensi.store'), $out + ['early_checkout_reason' => 'Tugas dinas'])->assertOk();
    }

    public function test_logging_contains_location_result_only_in_development(): void
    {
        $this->school();
        $this->actor();
        Log::spy();
        $this->postJson(route('mobile.role2.presensi.store'), $this->payload(50))->assertOk();
        Log::shouldHaveReceived('debug')->once()->with('Attendance geofence validation', \Mockery::on(fn ($data) =>
            $data['school_id'] === 101 && $data['location']['inside'] && $data['location']['accuracy_valid']
            && abs($data['location']['distance_meters'] - 50) < 0.001));
    }

    public function test_additive_migration_keeps_legacy_polygons_and_attendance_unchanged(): void
    {
        $migration = require database_path('migrations/2026_09_16_000000_add_radius_geofence_to_attendance_settings.php');
        $migration->down();
        $setting = app(AttendanceValidationService::class)->settingForKelas(101);
        $polygon = [[-8, 109], [-8, 111], [-6, 111], [-6, 109]];
        $setting->update(['geofence_polygon' => $polygon, 'max_gps_accuracy' => 25]);
        $this->actor();
        $this->postJson(route('mobile.role2.presensi.store'), $this->payload(10))->assertOk();
        $before = DB::table('attendances')->first();
        $migration->up();
        $setting->refresh();
        $this->assertSame('polygon', $setting->geofence_mode);
        $this->assertNull($setting->radius_meters);
        $this->assertSame($polygon, $setting->geofence_polygon);
        $this->assertEquals(25, $setting->max_gps_accuracy);
        $this->assertEquals($before, DB::table('attendances')->first());
    }

    public function test_school_diagnosis_reads_settings_and_compares_school_ids_without_writes(): void
    {
        $this->school(101, 50);
        $this->school(102, 200);
        $this->assertSame(0, \Illuminate\Support\Facades\Artisan::call('presensi:diagnose-school', ['school' => '101', '--compare' => ['102']]));
        $output = \Illuminate\Support\Facades\Artisan::output();
        $this->assertStringContainsString('"radius_meters": 50', $output);
        $this->assertStringContainsString('"radius_meters": 200', $output);
        $this->assertDatabaseCount('attendance_settings', 2);
        $this->assertDatabaseCount('attendances', 0);
    }

    public function test_user_supplied_karangpilang_polygon_accepts_corners_edges_and_positions_across_the_area(): void
    {
        $polygon = json_decode(file_get_contents(base_path('tests/fixtures/attendance-karangpilang.json')), true)['polygon'];
        $setting = $this->school();
        $setting->update(['geofence_mode' => 'polygon', 'geofence_polygon' => $polygon]);
        $center = ['lat' => array_sum(array_column($polygon, 'lat')) / 4, 'lng' => array_sum(array_column($polygon, 'lng')) / 4];
        $positions = [$center];
        foreach ($polygon as $index => $point) {
            $positions[] = $point;
            $next = $polygon[($index + 1) % 4];
            $positions[] = ['lat' => ($point['lat'] + $next['lat']) / 2, 'lng' => ($point['lng'] + $next['lng']) / 2];
            $positions[] = ['lat' => ($point['lat'] + $center['lat']) / 2, 'lng' => ($point['lng'] + $center['lng']) / 2];
        }
        foreach ($positions as $index => $position) {
            $this->actor($index + 1);
            $payload = ['latitude' => $position['lat'], 'longitude' => $position['lng'], 'gps_accuracy' => 8, 'check_type' => 'datang'];
            $this->travelTo(Carbon::parse('2026-09-16 07:00:00'));
            $this->postJson(route('mobile.role2.presensi.store'), $payload)->assertOk();
            $this->travelTo(Carbon::parse('2026-09-16 14:00:00'));
            $this->postJson(route('mobile.role2.presensi.store'), array_merge($payload, ['check_type' => 'pulang']))->assertOk();
        }
        $this->assertDatabaseCount('attendances', 13);
    }
}
