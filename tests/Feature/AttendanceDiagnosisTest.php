<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Services\AttendanceValidationService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AttendanceDiagnosisTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('nama_lengkap');
            $table->integer('role');
            $table->integer('kelas_id')->nullable();
            $table->string('password')->default('must-not-appear');
        });
        Schema::create('kelas', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kelas');
        });
        (require database_path('migrations/2026_04_23_072128_create_attendance_feature_tables.php'))->up();
        (require database_path('migrations/2026_04_23_120000_add_daily_check_columns_to_attendances_table.php'))->up();
        DB::table('users')->insert([
            ['id' => 1, 'email' => 'teacher@example.test', 'nama_lengkap' => 'Guru Uji', 'role' => 2, 'kelas_id' => 101],
            ['id' => 2, 'email' => 'other@example.test', 'nama_lengkap' => 'Guru Lain', 'role' => 2, 'kelas_id' => 102],
        ]);
        DB::table('kelas')->insert(['id' => 101, 'nama_kelas' => 'Sekolah Uji']);
    }

    private function setting(): void
    {
        app(AttendanceValidationService::class)->settingForKelas(101)->update([
            'max_gps_accuracy' => 30,
            'geofence_polygon' => [[-7, 110], [-7, 111], [-6, 111], [-6, 110]],
        ]);
    }

    private function record(array $attributes = []): void
    {
        Attendance::create(array_merge([
            'user_id' => 1, 'kelas_id' => 101, 'attendance_date' => '2026-09-10',
            'check_type' => 'datang', 'status' => 'ditolak', 'checked_at' => '2026-09-10 07:00:00',
            'latitude' => -7.00005, 'longitude' => 110.5, 'gps_accuracy' => 5,
            'rejection_code' => 'outside_geofence', 'rejection_reason' => 'Anda berada di luar area sekolah',
        ], $attributes));
    }

    private function diagnose(): array
    {
        $this->assertSame(0, Artisan::call('presensi:diagnose-user', ['email' => ' TEACHER@example.test ']));
        $output = Artisan::output();
        $this->assertStringNotContainsString('must-not-appear', $output);
        return json_decode($output, true, 512, JSON_THROW_ON_ERROR);
    }

    public function test_diagnosis_reads_only_the_requested_account_and_never_writes(): void
    {
        $this->setting();
        $this->record();
        $this->record(['user_id' => 2, 'kelas_id' => 102]);
        DB::enableQueryLog();
        $data = $this->diagnose();
        $queries = DB::getQueryLog();
        DB::disableQueryLog();
        foreach ($queries as $query) $this->assertMatchesRegularExpression('/^select\b/i', $query['query']);
        $this->assertSame('Sekolah Uji', $data['sekolah']['nama_kelas']);
        $this->assertCount(1, $data['aktivitas']);
        $event = $data['aktivitas'][0];
        $this->assertFalse($event['di_dalam_polygon_akun_sekarang']);
        $this->assertSame(5.6, $event['jarak_di_luar_polygon_akun_sekarang_meter']);
        $this->assertTrue($event['akurasi_memenuhi_batas_sekarang']);
        $this->assertSame('outside_geofence', $event['kode_penolakan']);
        $this->assertNull($event['waktu_diterima']);
        $this->assertDatabaseCount('attendances', 2);
    }

    public function test_missing_school_and_settings_are_reported_without_creating_them(): void
    {
        DB::table('users')->where('id', 1)->update(['kelas_id' => 999]);
        $this->record();
        $data = $this->diagnose();
        $this->assertNull($data['sekolah']);
        $this->assertNull($data['pengaturan']);
        $this->assertCount(3, $data['peringatan']);
        $this->assertFalse($data['aktivitas'][0]['sekolah_sama_dengan_akun_sekarang']);
        $this->assertNull($data['aktivitas'][0]['di_dalam_polygon_akun_sekarang']);
        $this->assertDatabaseCount('attendance_settings', 0);
        $this->assertDatabaseHas('users', ['id' => 1, 'kelas_id' => 999]);
    }

    public function test_daily_arrival_and_rejected_departure_use_their_own_coordinates(): void
    {
        $this->setting();
        $this->record([
            'status' => 'hadir', 'check_type' => 'pulang', 'latitude' => -8,
            'check_in_at' => '2026-09-10 07:00:00',
            'check_in_latitude' => -6.5, 'check_in_longitude' => 110.5, 'check_in_gps_accuracy' => 5,
            'check_out_latitude' => -7.00005, 'check_out_longitude' => 110.5, 'check_out_gps_accuracy' => 10,
            'check_out_rejection_code' => 'outside_geofence', 'check_out_rejection_reason' => 'Di luar batas.',
        ]);
        $data = $this->diagnose();
        $this->assertCount(2, $data['aktivitas']);
        $this->assertTrue($data['aktivitas'][0]['di_dalam_polygon_akun_sekarang']);
        $this->assertNull($data['aktivitas'][0]['kode_penolakan']);
        $this->assertSame(5.6, $data['aktivitas'][1]['jarak_di_luar_polygon_akun_sekarang_meter']);
        $this->assertSame('outside_geofence', $data['aktivitas'][1]['kode_penolakan']);
        $this->assertNull($data['aktivitas'][1]['waktu_diterima']);
    }

    public function test_absent_or_ambiguous_email_is_not_silently_resolved_to_another_account(): void
    {
        $this->assertSame(1, Artisan::call('presensi:diagnose-user', ['email' => 'absent@example.test']));
        $this->assertStringContainsString('tidak ditemukan', Artisan::output());
        DB::table('users')->where('id', 2)->update(['email' => 'TEACHER@example.test']);
        $this->assertSame(1, Artisan::call('presensi:diagnose-user', ['email' => 'teacher@example.test']));
        $this->assertStringContainsString('lebih dari satu akun', Artisan::output());
        $this->assertDatabaseCount('attendance_settings', 0);
    }
}
