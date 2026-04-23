<?php

namespace App\Services;

use App\Models\AttendanceSetting;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceValidationService
{
    public function settingForKelas($kelasId): AttendanceSetting
    {
        return AttendanceSetting::firstOrCreate(
            ['kelas_id' => $kelasId],
            [
                'enable_check_in' => true,
                'enable_check_out' => true,
                'enable_permission' => true,
                'check_in_time' => '07:00:00',
                'check_out_time' => '14:00:00',
                'late_tolerance_minutes' => 10,
                'max_gps_accuracy' => 2,
                'enable_fake_gps_detection' => true,
                'require_selfie' => false,
            ]
        );
    }

    public function validateAttendance(Request $request, AttendanceSetting $setting, string $checkType): array
    {
        $latitude = (float) $request->input('latitude');
        $longitude = (float) $request->input('longitude');
        $accuracy = (float) $request->input('gps_accuracy');
        $polygon = $this->normalizePolygon($setting->geofence_polygon);
        $mockLocation = $this->detectMockLocation($request);

        if ($checkType === 'datang' && !$setting->enable_check_in) {
            return $this->rejected('feature_disabled', 'Presensi datang sedang dinonaktifkan.');
        }

        if ($checkType === 'pulang' && !$setting->enable_check_out) {
            return $this->rejected('feature_disabled', 'Presensi pulang sedang dinonaktifkan.');
        }

        if (count($polygon) < 3) {
            return $this->rejected('geofence_not_configured', 'Area presensi sekolah belum diatur.');
        }

        $minimumGpsAccuracy = $this->minimumGpsAccuracy($setting);

        if ($accuracy <= 0 || $accuracy < $minimumGpsAccuracy) {
            return $this->rejected(
                'invalid_accuracy',
                'Akurasi lokasi ' . number_format($accuracy, 1) . ' m di bawah batas minimal ' . number_format($minimumGpsAccuracy, 1) . ' m.'
            );
        }

        if ($setting->enable_fake_gps_detection && $mockLocation['detected']) {
            return $this->rejected('fake_gps', 'Terdeteksi penggunaan lokasi palsu (Fake GPS)', [
                'is_mock_location' => true,
                'mock_detection_source' => $mockLocation['source'],
            ]);
        }

        if (!$this->pointInPolygon($latitude, $longitude, $polygon)) {
            return $this->rejected('outside_geofence', 'Anda berada di luar area sekolah');
        }

        $checkedAt = now();
        $status = 'hadir';
        $message = 'Presensi berhasil';
        $rejectionReason = null;

        if ($checkType === 'datang') {
            $deadline = Carbon::parse($checkedAt->toDateString() . ' ' . $setting->check_in_time)
                ->addMinutes((int) $setting->late_tolerance_minutes);

            if ($checkedAt->gt($deadline)) {
                $status = 'terlambat';
                $message = 'Presensi berhasil. Anda tercatat terlambat.';
                $rejectionReason = 'Terlambat';
            }
        }

        if ($checkType === 'pulang') {
            $checkOutTime = Carbon::parse($checkedAt->toDateString() . ' ' . $setting->check_out_time);

            if ($checkedAt->lt($checkOutTime)) {
                $earlyCheckoutReason = trim((string) $request->input('early_checkout_reason'));

                if ($earlyCheckoutReason === '') {
                    return $this->rejected('early_checkout_reason_required', 'Alasan pulang awal wajib diisi.');
                }

                $rejectionReason = 'Pulang awal: ' . $earlyCheckoutReason;
                $message = 'Presensi pulang awal berhasil dicatat.';
            }
        }

        return [
            'accepted' => true,
            'status' => $status,
            'message' => $message,
            'checked_at' => $checkedAt,
            'is_inside_geofence' => true,
            'is_mock_location' => false,
            'mock_detection_source' => null,
            'rejection_code' => null,
            'rejection_reason' => $rejectionReason,
        ];
    }

    public function normalizePolygon($polygon): array
    {
        if (is_string($polygon)) {
            $polygon = json_decode($polygon, true);
        }

        if (!is_array($polygon)) {
            return [];
        }

        $points = [];
        foreach ($polygon as $point) {
            if (is_array($point) && isset($point['lat'], $point['lng'])) {
                $points[] = ['lat' => (float) $point['lat'], 'lng' => (float) $point['lng']];
                continue;
            }

            if (is_array($point) && isset($point[0], $point[1])) {
                $points[] = ['lat' => (float) $point[0], 'lng' => (float) $point[1]];
            }
        }

        return $points;
    }

    public function pointInPolygon(float $latitude, float $longitude, array $polygon): bool
    {
        $inside = false;
        $pointsCount = count($polygon);

        for ($i = 0, $j = $pointsCount - 1; $i < $pointsCount; $j = $i++) {
            $latI = $polygon[$i]['lat'];
            $lngI = $polygon[$i]['lng'];
            $latJ = $polygon[$j]['lat'];
            $lngJ = $polygon[$j]['lng'];

            $intersects = (($lngI > $longitude) !== ($lngJ > $longitude))
                && ($latitude < ($latJ - $latI) * ($longitude - $lngI) / (($lngJ - $lngI) ?: 0.0000001) + $latI);

            if ($intersects) {
                $inside = !$inside;
            }
        }

        return $inside;
    }

    protected function detectMockLocation(Request $request): array
    {
        $fields = [
            'is_mock_location',
            'mock_location_detected',
            'mocked',
            'isFromMockProvider',
        ];

        foreach ($fields as $field) {
            if ($request->has($field) && filter_var($request->input($field), FILTER_VALIDATE_BOOLEAN)) {
                return ['detected' => true, 'source' => $field];
            }
        }

        return ['detected' => false, 'source' => null];
    }

    protected function minimumGpsAccuracy(AttendanceSetting $setting): float
    {
        $configuredAccuracy = (float) $setting->max_gps_accuracy;

        return $configuredAccuracy > 0 ? $configuredAccuracy : 2.0;
    }

    protected function rejected(string $code, string $message, array $extra = []): array
    {
        return array_merge([
            'accepted' => false,
            'status' => 'ditolak',
            'message' => $message,
            'checked_at' => now(),
            'is_inside_geofence' => $code !== 'outside_geofence' && $code !== 'geofence_not_configured',
            'is_mock_location' => false,
            'mock_detection_source' => null,
            'rejection_code' => $code,
            'rejection_reason' => $message,
        ], $extra);
    }
}
