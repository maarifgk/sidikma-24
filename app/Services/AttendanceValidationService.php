<?php

namespace App\Services;

use App\Models\AttendanceSetting;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceValidationService
{
    public const DEFAULT_MAX_GPS_ACCURACY = 100.0;

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
                'max_gps_accuracy' => self::DEFAULT_MAX_GPS_ACCURACY,
                'enable_fake_gps_detection' => true,
                'require_selfie' => false,
            ]
        );
    }

    public function validateAttendance(Request $request, AttendanceSetting $setting, string $checkType): array
    {
        $location = $this->validateAttendanceLocation($request->input('latitude'), $request->input('longitude'),
            $request->input('gps_accuracy'), $setting);
        $result = $this->evaluateAttendance($request, $setting, $checkType, $location);

        return array_merge($result, [
            'is_inside_geofence' => $location['inside'],
            'location' => $location,
        ]);
    }

    protected function evaluateAttendance(Request $request, AttendanceSetting $setting, string $checkType, array $location): array
    {
        $accuracy = (float) $request->input('gps_accuracy');
        $mockLocation = $this->detectMockLocation($request);

        if ($checkType === 'datang' && !$setting->enable_check_in) {
            return $this->rejected('feature_disabled', 'Presensi datang sedang dinonaktifkan.');
        }

        if ($checkType === 'pulang' && !$setting->enable_check_out) {
            return $this->rejected('feature_disabled', 'Presensi pulang sedang dinonaktifkan.');
        }

        if (!$location['configured']) {
            return $this->rejected('geofence_not_configured', 'Lokasi presensi sekolah belum dikonfigurasi dengan benar. Silakan hubungi admin sekolah.');
        }

        if (!$location['coordinates_valid']) {
            return $this->rejected('invalid_coordinates', 'Koordinat perangkat tidak valid. Ambil ulang lokasi lalu coba kembali.');
        }

        $maximumGpsAccuracy = $this->maximumGpsAccuracy($setting);

        if (!is_numeric($request->input('gps_accuracy')) || !is_finite($accuracy) || $accuracy < 0) {
            return $this->rejected('invalid_accuracy', 'Data akurasi GPS tidak valid. Ambil ulang lokasi lalu coba lagi.');
        }

        // Accuracy is uncertainty in meters: smaller values are MORE precise.
        // A school limit of 30 must accept a 5 m fix, including exactly 30 m.
        if ($accuracy > $maximumGpsAccuracy) {
            return $this->rejected(
                'invalid_accuracy',
                'Lokasi belum cukup akurat. Ketidakpastian GPS saat ini: ' . round($accuracy, 2)
                    . ' meter; batas sekolah: ' . $maximumGpsAccuracy
                    . ' meter. Aktifkan lokasi presisi, pindah ke tempat terbuka di area sekolah, lalu coba lagi.'
            );
        }

        if ($setting->enable_fake_gps_detection && $mockLocation['detected']) {
            return $this->rejected('fake_gps', 'Terdeteksi penggunaan lokasi palsu (Fake GPS)', [
                'is_mock_location' => true,
                'mock_detection_source' => $mockLocation['source'],
            ]);
        }

        if (!$location['inside']) {
            if ($location['mode'] === 'radius') {
                return $this->rejected('outside_geofence',
                    'Anda berada ' . round($location['distance_meters'], 2)
                    . ' meter dari titik sekolah. Radius presensi yang diperbolehkan adalah '
                    . $location['radius_meters'] . ' meter. Akurasi GPS: ' . round($accuracy, 2) . ' meter.');
            }
            $distance = $location['distance_meters'];

            return $this->rejected('outside_geofence',
                'Posisi GPS terbaca sekitar ' . round($distance, 1)
                . ' meter di luar batas area sekolah. Ketidakpastian GPS: ' . round($accuracy, 2)
                . ' meter. Coba ulang di tempat terbuka dalam sekolah. Jika posisi sudah benar, minta admin memeriksa polygon sekolah.'
            );
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

    public function locationContext(AttendanceSetting $setting, int $userId): array
    {
        $context = [
            'school_id' => (int) $setting->kelas_id,
            'polygon' => $this->normalizePolygon($setting->geofence_polygon),
            'geofence' => $this->getSchoolGeofence($setting),
            'max_gps_accuracy' => $this->maximumGpsAccuracy($setting),
        ];
        // Detect settings changes between viewing the area and submitting a fix.
        // The client never supplies the polygon used for acceptance.
        $context['version'] = hash('sha256', $userId . ':' . json_encode($context, JSON_THROW_ON_ERROR));

        return $context;
    }

    public function getSchoolGeofence(AttendanceSetting $setting): array
    {
        $mode = $setting->geofence_mode ?? 'polygon';
        if ($mode === 'polygon') {
            return ['mode' => 'polygon', 'polygon' => $this->normalizePolygon($setting->geofence_polygon)];
        }

        return [
            'mode' => $mode,
            'center_latitude' => $setting->center_latitude,
            'center_longitude' => $setting->center_longitude,
            'radius_meters' => $setting->radius_meters,
        ];
    }

    public function calculateDistance(float $latFrom, float $lngFrom, float $latTo, float $lngTo): float
    {
        $latDelta = deg2rad($latTo - $latFrom);
        $lngDelta = deg2rad($lngTo - $lngFrom);
        $a = sin($latDelta / 2) ** 2
            + cos(deg2rad($latFrom)) * cos(deg2rad($latTo)) * sin($lngDelta / 2) ** 2;

        return 6371000 * 2 * asin(sqrt(max(0, min(1, $a))));
    }

    public function validCoordinates($latitude, $longitude): bool
    {
        return is_numeric($latitude) && is_numeric($longitude)
            && is_finite((float) $latitude) && is_finite((float) $longitude)
            && abs((float) $latitude) <= 90 && abs((float) $longitude) <= 180;
    }

    public function validateGpsAccuracy($accuracy, AttendanceSetting $setting): bool
    {
        return is_numeric($accuracy) && is_finite((float) $accuracy)
            && (float) $accuracy >= 0 && (float) $accuracy <= $this->maximumGpsAccuracy($setting);
    }

    public function validateAttendanceLocation($latitude, $longitude, $accuracy, AttendanceSetting $setting): array
    {
        $geofence = $this->getSchoolGeofence($setting);
        $coordinatesValid = $this->validCoordinates($latitude, $longitude);
        $configured = false;
        $inside = false;
        $distance = null;

        if ($geofence['mode'] === 'radius') {
            $radius = $geofence['radius_meters'];
            $configured = $this->validCoordinates($geofence['center_latitude'], $geofence['center_longitude'])
                && is_numeric($radius) && is_finite((float) $radius) && $radius > 0;
            if ($configured && $coordinatesValid) {
                $distance = $this->calculateDistance($geofence['center_latitude'], $geofence['center_longitude'],
                    (float) $latitude, (float) $longitude);
                // Microscopic floating-point epsilon only, never GPS-based radius expansion.
                $inside = $distance <= $radius + 1e-7;
            }
        } elseif ($geofence['mode'] === 'polygon') {
            $configured = count($geofence['polygon']) >= 3;
            if ($configured && $coordinatesValid) {
                $inside = $this->pointInPolygon((float) $latitude, (float) $longitude, $geofence['polygon']);
                $distance = $this->distanceToPolygon((float) $latitude, (float) $longitude, $geofence['polygon']);
            }
        }

        return [
            'mode' => $geofence['mode'], 'configured' => $configured, 'inside' => $inside,
            'coordinates_valid' => $coordinatesValid,
            'distance_meters' => $distance,
            'radius_meters' => $geofence['radius_meters'] ?? null,
            'accuracy_valid' => $this->validateGpsAccuracy($accuracy, $setting),
            'max_gps_accuracy' => $this->maximumGpsAccuracy($setting),
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
            if (!is_array($point)) {
                return [];
            }
            $lat = $point['lat'] ?? $point[0] ?? null;
            $lng = $point['lng'] ?? $point[1] ?? null;
            if (!is_numeric($lat) || !is_numeric($lng)
                || !is_finite((float) $lat) || !is_finite((float) $lng)
                || abs((float) $lat) > 90 || abs((float) $lng) > 180) {
                return [];
            }
            $points[] = ['lat' => (float) $lat, 'lng' => (float) $lng];
        }

        if (count($points) < 3) {
            return [];
        }
        // Reject collapsed polygons; compute relative to the first point to
        // avoid cancellation at small school boundaries.
        $area = 0;
        $origin = $points[0];
        for ($i = 0, $j = count($points) - 1; $i < count($points); $j = $i++) {
            $area += ($points[$j]['lng'] - $origin['lng']) * ($points[$i]['lat'] - $origin['lat'])
                - ($points[$i]['lng'] - $origin['lng']) * ($points[$j]['lat'] - $origin['lat']);
        }

        return abs($area) > 1e-16 ? $points : [];
    }

    public function pointInPolygon(float $latitude, float $longitude, array $polygon): bool
    {
        $polygon = $this->normalizePolygon($polygon);
        if (count($polygon) < 3) {
            return false;
        }

        // Include the boundary, allowing only 1 cm for coordinate rounding.
        // GPS uncertainty never expands the permitted school area.
        if ($this->distanceToPolygon($latitude, $longitude, $polygon) <= 0.01) {
            return true;
        }

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

    public function distanceToPolygon(float $latitude, float $longitude, array $polygon): float
    {
        // Local tangent-plane approximation, suitable for school-sized polygons.
        $metersPerDegree = 6371000 * M_PI / 180;
        $longitudeScale = $metersPerDegree * cos(deg2rad($latitude));
        $distance = INF;

        for ($i = 0, $j = count($polygon) - 1; $i < count($polygon); $j = $i++) {
            $ax = ($polygon[$j]['lng'] - $longitude) * $longitudeScale;
            $ay = ($polygon[$j]['lat'] - $latitude) * $metersPerDegree;
            $bx = ($polygon[$i]['lng'] - $longitude) * $longitudeScale;
            $by = ($polygon[$i]['lat'] - $latitude) * $metersPerDegree;
            $dx = $bx - $ax;
            $dy = $by - $ay;
            $lengthSquared = $dx * $dx + $dy * $dy;
            $projection = $lengthSquared > 0
                ? max(0, min(1, -($ax * $dx + $ay * $dy) / $lengthSquared)) : 0;
            $distance = min($distance, hypot($ax + $projection * $dx, $ay + $projection * $dy));
        }

        return $distance;
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

    public function maximumGpsAccuracy(AttendanceSetting $setting): float
    {
        $configuredAccuracy = (float) $setting->max_gps_accuracy;

        return is_finite($configuredAccuracy) && $configuredAccuracy > 0
            ? $configuredAccuracy
            : self::DEFAULT_MAX_GPS_ACCURACY;
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
