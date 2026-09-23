<?php

// CLI only. This checks the loaded validator using synthetic data, without a DB.
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$validator = $app->make(App\Services\AttendanceValidationService::class);
$setting = new App\Models\AttendanceSetting([
    'enable_check_in' => true,
    'max_gps_accuracy' => 30,
    'enable_fake_gps_detection' => true,
    'check_in_time' => '23:59:59',
    'late_tolerance_minutes' => 0,
    'geofence_polygon' => [[-7, 110], [-7, 111], [-6, 111], [-6, 110]],
]);
$checks = [];
$passed = true;
foreach ([5, 30, 31] as $accuracy) {
    $result = $validator->validateAttendance(Illuminate\Http\Request::create('/', 'POST', [
        'latitude' => -6.5,
        'longitude' => 110.5,
        'gps_accuracy' => $accuracy,
    ]), $setting, 'datang');
    $correct = $accuracy <= 30
        ? $result['accepted'] === true
        : $result['accepted'] === false && $result['rejection_code'] === 'invalid_accuracy';
    $passed = $passed && $correct;
    $checks[] = ['gps_meter' => $accuracy, 'hasil' => $result['accepted'] ? 'diterima' : 'ditolak',
        'sesuai' => $correct, 'pesan' => $result['message']];
}

echo json_encode([
    'validasi_maksimum_gps_benar' => $passed,
    'file_validator_cli' => (new ReflectionClass($validator))->getFileName(),
    'batas_meter' => 30,
    'pemeriksaan' => $checks,
    'catatan' => 'Pemeriksaan proses PHP CLI, bukan proses web. Tidak membaca atau mengubah akun, pengaturan, dan presensi. Jika browser masih menampilkan pesan lama, periksa folder aplikasi domain dan muat ulang PHP/OPcache hosting.',
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR) . PHP_EOL;
exit($passed ? 0 : 1);
