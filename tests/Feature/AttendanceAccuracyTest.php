<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\AttendanceSetting;
use App\Models\User;
use App\Services\AttendanceValidationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Tests\TestCase;

class AttendanceAccuracyTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Exercise real requests and persistence using an isolated in-memory DB.
        config([
            'app.key' => str_repeat('a', 32),
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
        ]);
        DB::purge('sqlite');
        Schema::create('kelas', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kelas');
        });

        (require database_path('migrations/2026_04_23_072128_create_attendance_feature_tables.php'))->up();
        (require database_path('migrations/2026_04_23_120000_add_daily_check_columns_to_attendances_table.php'))->up();

        $this->travelTo(Carbon::parse('2026-09-09 07:00:00', 'Asia/Jakarta'));
    }

    private function school(int $kelasId, float $limit = 30): AttendanceSetting
    {
        DB::table('kelas')->insertOrIgnore(['id' => $kelasId, 'nama_kelas' => 'Sekolah Uji ' . $kelasId]);
        $setting = app(AttendanceValidationService::class)->settingForKelas($kelasId);
        $setting->update([
            'max_gps_accuracy' => $limit,
            // Synthetic test area; these are not actual school coordinates.
            'geofence_polygon' => [[-7, 110], [-7, 111], [-6, 111], [-6, 110]],
        ]);

        return $setting;
    }

    private function teacher(int $userId, int $kelasId): void
    {
        $this->actingAs((new User())->forceFill([
            'id' => $userId,
            'role' => 2,
            'kelas_id' => $kelasId,
        ]));
    }

    private function payload(float $accuracy, array $extra = []): array
    {
        return array_merge([
            'check_type' => 'datang',
            'latitude' => -6.5,
            'longitude' => 110.5,
            'gps_accuracy' => $accuracy,
        ], $extra);
    }

    public function test_teachers_in_two_schools_can_check_in_and_out_with_a_thirty_meter_limit(): void
    {
        foreach ([101, 102] as $kelasId) {
            $setting = $this->school($kelasId);

            foreach ([0.5, 5, 10, 29.99, 30] as $index => $accuracy) {
                $userId = $kelasId * 10 + $index;
                $this->teacher($userId, $kelasId);
                $this->travelTo(Carbon::parse('2026-09-09 07:00:00', 'Asia/Jakarta'));

                $this->postJson(route('mobile.role2.presensi.store'), $this->payload($accuracy))
                    ->assertOk()->assertJson(['success' => true, 'status' => 'hadir']);

                $this->travelTo(Carbon::parse('2026-09-09 14:00:00', 'Asia/Jakarta'));
                $this->postJson(route('mobile.role2.presensi.store'), $this->payload($accuracy, ['check_type' => 'pulang']))
                    ->assertOk()->assertJson(['success' => true]);

                $attendance = Attendance::where('user_id', $userId)->sole();
                $this->assertNotNull($attendance->check_in_at);
                $this->assertNotNull($attendance->check_out_at);
                $this->assertSame((float) $accuracy, $attendance->check_in_gps_accuracy);
                $this->assertSame((float) $accuracy, $attendance->check_out_gps_accuracy);
                $this->assertSame('hadir', $attendance->status);
            }

            $this->assertSame(30.0, $setting->fresh()->max_gps_accuracy);
        }
    }

    public function test_rejected_accuracy_does_not_block_a_more_precise_retry(): void
    {
        $this->school(101);
        $this->teacher(1, 101);

        $this->postJson(route('mobile.role2.presensi.store'), $this->payload(30.1))
            ->assertStatus(422)->assertJson(['success' => false, 'status' => 'ditolak']);
        $this->assertDatabaseHas('attendances', ['user_id' => 1, 'rejection_code' => 'invalid_accuracy']);

        $this->postJson(route('mobile.role2.presensi.store'), $this->payload(5))
            ->assertOk()->assertJson(['success' => true, 'status' => 'hadir']);
        $this->assertDatabaseCount('attendances', 1);
        $this->assertDatabaseHas('attendances', ['user_id' => 1, 'status' => 'hadir', 'rejection_code' => null]);
    }

    public function test_precise_locations_still_need_to_be_inside_school_and_not_mocked(): void
    {
        $this->school(101);
        $this->teacher(1, 101);

        $this->postJson(route('mobile.role2.presensi.store'), $this->payload(5, ['latitude' => -8]))
            ->assertStatus(422)->assertJson(['success' => false]);
        $this->assertDatabaseHas('attendances', ['rejection_code' => 'outside_geofence']);

        $this->postJson(route('mobile.role2.presensi.store'), $this->payload(5, ['is_mock_location' => true]))
            ->assertStatus(422)->assertJson(['success' => false]);
        $this->assertDatabaseHas('attendances', ['rejection_code' => 'fake_gps']);
        $this->assertSame(0, Attendance::whereNotNull('check_in_at')->count());
    }

    public function test_each_school_keeps_its_own_accuracy_setting(): void
    {
        $strictSchool = $this->school(101, 10);
        $otherSchool = $this->school(102, 30);
        $this->teacher(1, 101);

        $this->postJson(route('mobile.role2.presensi.store'), $this->payload(20))
            ->assertStatus(422)->assertJson(['success' => false]);

        $this->teacher(2, 102);
        $this->postJson(route('mobile.role2.presensi.store'), $this->payload(20))
            ->assertOk()->assertJson(['success' => true]);

        $this->assertSame(10.0, $strictSchool->fresh()->max_gps_accuracy);
        $this->assertSame(30.0, $otherSchool->fresh()->max_gps_accuracy);
    }

    public function test_precise_outside_location_is_rejected_and_can_retry_on_the_school_boundary(): void
    {
        $this->school(101);
        $this->teacher(1, 101);
        $response = $this->postJson(route('mobile.role2.presensi.store'), $this->payload(5, ['latitude' => -7.00005]));
        $response->assertStatus(422)->assertJson(['success' => false]);
        $this->assertStringContainsString('5.6 meter di luar batas', $response->json('message'));
        $this->assertNull(Attendance::sole()->check_in_time);

        $this->postJson(route('mobile.role2.presensi.store'), $this->payload(5, ['latitude' => -6]))
            ->assertOk()->assertJson(['success' => true]);
        $this->assertNotNull(Attendance::sole()->check_in_time);
    }

    public function test_rejected_arrival_remains_retryable_after_a_successful_departure(): void
    {
        $this->school(101);
        $this->teacher(1, 101);
        $this->postJson(route('mobile.role2.presensi.store'), $this->payload(5, ['latitude' => -8]))
            ->assertStatus(422)->assertJson(['success' => false]);

        $this->travelTo(Carbon::parse('2026-09-09 14:00:00', 'Asia/Jakarta'));
        $this->postJson(route('mobile.role2.presensi.store'), $this->payload(5, ['check_type' => 'pulang']))
            ->assertOk()->assertJson(['success' => true]);

        $attendance = Attendance::sole();
        $this->assertNull($attendance->check_in_at);
        $this->assertNull($attendance->check_in_time, 'A rejected arrival must not become accepted when departure succeeds.');
        $this->assertNotNull($attendance->check_out_time);

        $this->postJson(route('mobile.role2.presensi.store'), $this->payload(5))
            ->assertOk()->assertJson(['success' => true, 'status' => 'terlambat']);
        $attendance = Attendance::sole();
        $this->assertSame('14:00:00', $attendance->check_in_time->format('H:i:s'));
        $this->assertNotNull($attendance->check_out_at);
        $this->assertNull($attendance->check_in_rejection_code);
    }

    public function test_outside_departure_can_retry_without_changing_an_accepted_arrival(): void
    {
        $this->school(101);
        $this->teacher(1, 101);
        $this->postJson(route('mobile.role2.presensi.store'), $this->payload(5))->assertOk();
        $this->travelTo(Carbon::parse('2026-09-09 14:00:00', 'Asia/Jakarta'));
        $this->postJson(route('mobile.role2.presensi.store'), $this->payload(5, ['check_type' => 'pulang', 'latitude' => -8]))
            ->assertStatus(422)->assertJson(['success' => false]);
        $this->assertNull(Attendance::sole()->check_out_time);

        $this->postJson(route('mobile.role2.presensi.store'), $this->payload(5, ['check_type' => 'pulang']))
            ->assertOk()->assertJson(['success' => true]);
        $attendance = Attendance::sole();
        $this->assertSame('07:00:00', $attendance->check_in_time->format('H:i:s'));
        $this->assertSame('14:00:00', $attendance->check_out_time->format('H:i:s'));
        $this->assertSame('hadir', $attendance->status);
        $this->assertNull($attendance->check_out_rejection_code);
    }

    public function test_location_context_is_current_and_scoped_to_the_authenticated_teachers_school(): void
    {
        $setting = $this->school(101);
        $this->school(102, 10);
        $this->teacher(1, 101);
        $response = $this->getJson(route('mobile.role2.presensi.location-context', ['kelas_id' => 102]));
        $response->assertOk()->assertJsonPath('user_id', 1)->assertJsonPath('location_context.school_id', 101)
            ->assertJsonPath('location_context.max_gps_accuracy', 30);
        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));
        $first = $response->json('location_context');
        $this->assertSame(['lat' => -7, 'lng' => 110], $first['polygon'][0]);
        $this->assertSame(64, strlen($first['version']));
        $setting->update(['max_gps_accuracy' => 20]);
        $new = $this->getJson(route('mobile.role2.presensi.location-context'))->assertOk()->json('location_context');
        $this->assertNotSame($first['version'], $new['version']);
        $this->assertDatabaseCount('attendances', 0);
        $this->assertDatabaseCount('attendance_settings', 2);
    }

    public function test_polygon_change_during_gps_acquisition_does_not_create_a_false_outside_rejection(): void
    {
        $setting = $this->school(101);
        $this->teacher(1, 101);
        $oldContext = $this->getJson(route('mobile.role2.presensi.location-context'))->assertOk()->json('location_context');
        // The open page showed latitude -6.5 inside; the saved area then moved.
        $setting->update(['geofence_polygon' => [[-8, 110], [-8, 111], [-7, 111], [-7, 110]]]);
        $this->postJson(route('mobile.role2.presensi.store'), $this->payload(5, ['location_settings_version' => $oldContext['version']]))
            ->assertStatus(409)->assertJsonPath('rejection_code', 'location_settings_changed');
        $this->assertDatabaseCount('attendances', 0);

        $freshContext = $this->getJson(route('mobile.role2.presensi.location-context'))->assertOk()->json('location_context');
        $this->postJson(route('mobile.role2.presensi.store'), $this->payload(5, [
            'latitude' => -7.5, 'location_settings_version' => $freshContext['version'],
        ]))->assertOk()->assertJson(['success' => true, 'status' => 'hadir']);
        $this->assertSame(-7.5, Attendance::sole()->check_in_latitude);
    }

    public function test_a_current_context_cannot_override_the_saved_polygon_or_accuracy_limit(): void
    {
        $this->school(101);
        $this->teacher(1, 101);
        $context = $this->getJson(route('mobile.role2.presensi.location-context'))->assertOk()->json('location_context');
        $this->postJson(route('mobile.role2.presensi.store'), $this->payload(5, [
            'latitude' => -8, 'location_settings_version' => $context['version'],
            'geofence_polygon' => [[-9, 109], [-9, 112], [-5, 112], [-5, 109]],
            'is_inside_geofence' => true,
        ]))->assertStatus(422)->assertJsonPath('rejection_code', 'outside_geofence');
        $this->postJson(route('mobile.role2.presensi.store'), $this->payload(31, [
            'location_settings_version' => $context['version'],
        ]))->assertStatus(422)->assertJsonPath('rejection_code', 'invalid_accuracy');
        $this->assertNull(Attendance::sole()->check_in_at);
    }

    public function test_location_context_cannot_be_reused_after_switching_accounts_even_in_the_same_school(): void
    {
        $this->school(101);
        $this->teacher(1, 101);
        $context = $this->getJson(route('mobile.role2.presensi.location-context'))->assertOk()->json('location_context');
        $this->teacher(2, 101);
        $this->postJson(route('mobile.role2.presensi.store'), $this->payload(5, [
            'location_settings_version' => $context['version'],
        ]))->assertStatus(409)->assertJsonPath('rejection_code', 'location_settings_changed');
        $this->assertDatabaseCount('attendances', 0);
    }

    public function test_missing_school_settings_are_reported_without_creating_them_and_non_teachers_cannot_read_context(): void
    {
        $this->teacher(1, 999);
        $this->getJson(route('mobile.role2.presensi.location-context'))->assertStatus(422);
        $this->assertDatabaseCount('attendance_settings', 0);
        $this->actingAs((new User())->forceFill(['id' => 2, 'role' => 3, 'kelas_id' => 999]));
        $this->getJson(route('mobile.role2.presensi.location-context'))->assertForbidden();
    }
}
