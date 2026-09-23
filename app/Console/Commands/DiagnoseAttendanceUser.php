<?php

namespace App\Console\Commands;

use App\Models\AttendanceSetting;
use App\Services\AttendanceValidationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Output\OutputInterface;

class DiagnoseAttendanceUser extends Command
{
    protected $signature = 'presensi:diagnose-user {email : Email akun guru yang diperiksa}';

    protected $description = 'Baca sekolah, pengaturan, dan lokasi presensi terbaru satu akun tanpa mengubah data';

    public function handle(AttendanceValidationService $validation): int
    {
        $email = strtolower(trim((string) $this->argument('email')));
        $users = DB::table('users')->select('id', 'nama_lengkap', 'email', 'role', 'kelas_id')
            ->whereRaw('LOWER(TRIM(email)) = ?', [$email])->limit(2)->get();
        if ($users->count() !== 1) {
            $this->error($users->isEmpty() ? 'Akun tidak ditemukan.' : 'Email terhubung ke lebih dari satu akun. Periksa data pengguna.');
            return self::FAILURE;
        }

        $user = $users->first();
        $school = DB::table('kelas')->select('id', 'nama_kelas')->where('id', $user->kelas_id)->first();
        // Do not call settingForKelas(): diagnosis must never create settings.
        $setting = AttendanceSetting::where('kelas_id', $user->kelas_id)->first();
        $polygon = $validation->normalizePolygon($setting?->geofence_polygon);
        $warnings = [];
        if ((int) $user->role !== 2) $warnings[] = 'Akun ini bukan akun guru (role 2).';
        if (!$school) $warnings[] = 'Akun belum terhubung ke sekolah yang tersedia.';
        if (!$setting) $warnings[] = 'Pengaturan presensi sekolah belum tersedia.';
        $geofence = $setting ? $validation->getSchoolGeofence($setting) : null;
        if (!$setting || !$validation->validateAttendanceLocation(0, 0, 0, $setting)['configured']) {
            $warnings[] = 'Area presensi sekolah belum valid.';
        }
        if ($setting && !$setting->enable_check_in) $warnings[] = 'Presensi datang sekolah dinonaktifkan.';
        if ($setting && !$setting->enable_check_out) $warnings[] = 'Presensi pulang sekolah dinonaktifkan.';

        $fields = ['id', 'kelas_id', 'attendance_date', 'status', 'check_type', 'checked_at',
            'latitude', 'longitude', 'gps_accuracy', 'rejection_code', 'rejection_reason'];
        foreach (['check_in', 'check_out'] as $prefix) {
            foreach (['at', 'latitude', 'longitude', 'gps_accuracy', 'rejection_code', 'rejection_reason'] as $field) {
                $fields[] = $prefix . '_' . $field;
            }
        }
        $records = DB::table('attendances')->select($fields)->where('user_id', $user->id)
            ->orderByDesc('attendance_date')->orderByDesc('updated_at')->orderByDesc('id')->limit(5)->get();
        $events = [];
        foreach ($records as $record) {
            foreach (['datang' => 'check_in', 'pulang' => 'check_out'] as $type => $prefix) {
                $hasEventCoordinates = $record->{$prefix . '_latitude'} !== null && $record->{$prefix . '_longitude'} !== null;
                if (!$hasEventCoordinates && $record->check_type !== $type) continue;
                $latitude = $hasEventCoordinates ? $record->{$prefix . '_latitude'} : $record->latitude;
                $longitude = $hasEventCoordinates ? $record->{$prefix . '_longitude'} : $record->longitude;
                $accuracy = $hasEventCoordinates ? $record->{$prefix . '_gps_accuracy'} : $record->gps_accuracy;
                $validCoordinates = is_numeric($latitude) && is_numeric($longitude)
                    && is_finite((float) $latitude) && is_finite((float) $longitude)
                    && abs((float) $latitude) <= 90 && abs((float) $longitude) <= 180;
                $inside = $polygon && $validCoordinates
                    ? $validation->pointInPolygon((float) $latitude, (float) $longitude, $polygon) : null;
                $location = $setting ? $validation->validateAttendanceLocation($latitude, $longitude, $accuracy, $setting) : null;
                $events[] = [
                    'record_id' => $record->id,
                    'tanggal' => $record->attendance_date,
                    'jenis' => $type,
                    'status_baris' => $record->status,
                    'waktu_diterima' => $record->{$prefix . '_at'} ?? (!$hasEventCoordinates && $record->status !== 'ditolak' && $record->check_type === $type && !$record->rejection_code ? $record->checked_at : null),
                    'sekolah_id_saat_presensi' => $record->kelas_id,
                    'sekolah_sama_dengan_akun_sekarang' => (string) $record->kelas_id === (string) $user->kelas_id,
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'ketidakpastian_gps_meter' => $accuracy,
                    'kode_penolakan' => $hasEventCoordinates ? $record->{$prefix . '_rejection_code'} : $record->rejection_code,
                    'pesan_tersimpan' => $hasEventCoordinates ? $record->{$prefix . '_rejection_reason'} : $record->rejection_reason,
                    'di_dalam_polygon_akun_sekarang' => $inside,
                    'jarak_di_luar_polygon_akun_sekarang_meter' => $inside === null ? null : ($inside ? 0 : round($validation->distanceToPolygon((float) $latitude, (float) $longitude, $polygon), 1)),
                    'akurasi_memenuhi_batas_sekarang' => $setting && is_numeric($accuracy) && is_finite((float) $accuracy)
                        ? (float) $accuracy >= 0 && (float) $accuracy <= $validation->maximumGpsAccuracy($setting) : null,
                    'pemeriksaan_area_aktif_sekarang' => $location,
                ];
            }
        }

        $this->output->writeln(json_encode([
            'akun' => $user,
            'sekolah' => $school,
            'pengaturan' => $setting ? [
                'datang_aktif' => $setting->enable_check_in,
                'pulang_aktif' => $setting->enable_check_out,
                'batas_maksimum_gps_meter' => $validation->maximumGpsAccuracy($setting),
                'polygon' => $polygon,
                'geofence_aktif' => $geofence,
                'diubah_pada' => $setting->updated_at?->toIso8601String(),
            ] : null,
            'peringatan' => $warnings,
            'catatan' => 'Perbandingan memakai polygon dan batas GPS akun saat ini. Pengaturan atau sekolah mungkin berubah sejak presensi lama. Maksimal 5 baris terbaru; percobaan ulang dapat menimpa penolakan sebelumnya. Perintah ini tidak mengubah akun, pengaturan, atau presensi.',
            'aktivitas' => $events,
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR), OutputInterface::OUTPUT_RAW);

        return self::SUCCESS;
    }
}
