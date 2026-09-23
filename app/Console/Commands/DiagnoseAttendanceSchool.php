<?php

namespace App\Console\Commands;

use App\Models\AttendanceSetting;
use App\Services\AttendanceValidationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DiagnoseAttendanceSchool extends Command
{
    protected $signature = 'presensi:diagnose-school {school : ID atau bagian nama sekolah} {--compare=* : ID sekolah pembanding}';
    protected $description = 'Baca konfigurasi area, batas GPS, dan jumlah akun per sekolah tanpa mengubah data';

    public function handle(AttendanceValidationService $validation): int
    {
        try {
            $school = trim((string) $this->argument('school'));
            $schools = DB::table('kelas')->select('id', 'nama_kelas')
                ->where(function ($query) use ($school) {
                    if (ctype_digit($school)) $query->where('id', (int) $school);
                    else $query->whereRaw('LOWER(nama_kelas) LIKE ?', ['%' . strtolower($school) . '%']);
                    if ($this->option('compare')) $query->orWhereIn('id', $this->option('compare'));
                })->orderBy('id')->limit(20)->get();
            if ($schools->isEmpty()) {
                $this->error('Sekolah tidak ditemukan. Periksa nama atau gunakan ID sekolah.');
                return self::FAILURE;
            }
            $result = $schools->map(function ($school) use ($validation) {
                $setting = AttendanceSetting::where('kelas_id', $school->id)->first();
                return [
                    'school_id' => $school->id, 'school_name' => $school->nama_kelas,
                    'attendance_setting_id' => $setting?->id,
                    'geofence' => $setting ? $validation->getSchoolGeofence($setting) : null,
                    'area_configured' => $setting ? $validation->validateAttendanceLocation(0, 0, 0, $setting)['configured'] : false,
                    'max_gps_accuracy' => $setting ? $validation->maximumGpsAccuracy($setting) : null,
                    'check_in_enabled' => $setting?->enable_check_in, 'check_out_enabled' => $setting?->enable_check_out,
                    'teacher_count' => DB::table('users')->where('kelas_id', $school->id)->where('role', 2)->count(),
                    'admin_count' => DB::table('users')->where('kelas_id', $school->id)->where('role', 3)->count(),
                    'updated_at' => $setting?->updated_at?->toIso8601String(),
                ];
            });
            $this->line(json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR));
            $this->info('Hanya membaca konfigurasi saat ini. Gunakan diagnose-user untuk memeriksa relasi dan percobaan satu akun.');
            return self::SUCCESS;
        } catch (\Illuminate\Database\QueryException | \PDOException $error) {
            $this->error('Database belum dapat dibaca. Periksa koneksi dan ketersediaan tabel presensi pada lingkungan ini.');
            return self::FAILURE;
        }
    }
}
