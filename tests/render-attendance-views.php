<?php

// Render page bodies with synthetic data, without the database-backed site layout.
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
config(['session.driver' => 'array']);
session()->start();
$message = "Tersimpan: O'Neil \"Sekolah\" & <area>\nBaris kedua </script>";
session()->put('success', $message);
request()->setUserResolver(fn () => (new App\Models\User())->forceFill([
    'id' => 1, 'role' => 2, 'email' => 'teacher@example.test', 'image' => null,
]));
$polygon = [['lat' => -7, 'lng' => 110], ['lat' => -7, 'lng' => 111], ['lat' => -6, 'lng' => 111]];
$setting = new App\Models\AttendanceSetting([
    'geofence_polygon' => $polygon, 'max_gps_accuracy' => 30,
    'check_in_time' => '07:00:00', 'check_out_time' => '14:00:00',
    'late_tolerance_minutes' => 10, 'enable_check_in' => true, 'enable_check_out' => true,
    'enable_permission' => true, 'require_selfie' => true, 'enable_fake_gps_detection' => true,
]);
$attendance = (new App\Models\Attendance())->forceFill([
    'id' => 1, 'kelas_id' => 101, 'attendance_date' => '2026-09-16', 'status' => 'hadir',
    'check_type' => 'datang', 'checked_at' => '2026-09-16 07:00:00',
    'latitude' => -7, 'longitude' => 110, 'gps_accuracy' => 5,
    'nama_lengkap' => $message, 'nama_kelas' => $message,
]);
$permission = (new App\Models\AttendancePermission())->forceFill([
    'id' => 1, 'start_date' => '2026-09-16', 'end_date' => '2026-09-16',
    'created_at' => '2026-09-16 06:00:00', 'category' => 'sakit', 'status' => 'pending',
    'reason' => $message, 'nama_lengkap' => $message, 'nama_kelas' => $message,
]);
$data = [
    'setting' => $setting, 'geofencePolygon' => $polygon,
    'profile' => (object) ['nama_lengkap' => $message, 'nama_kelas' => $message],
    'canSelectKelas' => true, 'selectedKelasId' => 101, 'selectedKelasName' => $message,
    'classes' => collect([(object) ['id' => 101, 'nama_kelas' => $message]]),
    'users' => collect([(object) ['id' => 1, 'nama_lengkap' => $message]]),
    'errors' => new Illuminate\Support\ViewErrorBag(),
    'stats' => array_fill_keys(['total_hadir', 'hadir', 'terlambat', 'izin', 'cuti', 'tidak_hadir', 'persentase'], 1),
    'chart' => collect([['date' => '16/09', 'hadir' => 1, 'izin' => 0]]),
    'attendanceMapPoints' => collect([[
        'name' => $message, 'school_name' => $message, 'check_label' => 'Presensi datang',
        'checked_at' => '07:00', 'status' => 'hadir', 'latitude' => -7, 'longitude' => 110, 'gps_accuracy' => 5,
    ]]),
    'attendances' => collect([$attendance]), 'latestActivities' => collect([$attendance]),
    'history' => collect([$attendance]), 'todayAttendances' => collect(),
    'permissions' => collect([$permission]), 'status' => 'pending',
    'summary' => ['pending' => 1, 'approved' => 0, 'rejected' => 0, 'all' => 1],
    'filters' => ['kelasId' => 101, 'period' => 'harian', 'periodDate' => '2026-09-16',
        'from' => '2026-09-16', 'to' => '2026-09-16', 'userId' => null, 'status' => null],
];

function renderAttendancePage(string $path, array $data): array
{
    extract($data);
    $__env = app('view');
    $source = file_get_contents(resource_path('views/' . $path));
    $source = preg_replace('/@extends\([^\r\n]+\)/', '', $source);
    $source = str_replace(["@section('content')", "@section('js')", '@endsection'], '', $source);
    $compiled = app('blade.compiler')->compileString($source);
    ob_start();
    try {
        eval('?>' . $compiled);
        $html = ob_get_contents();
    } finally {
        ob_end_clean();
    }
    $document = new DOMDocument();
    $previous = libxml_use_internal_errors(true);
    $document->loadHTML('<?xml encoding="UTF-8">' . $html);
    libxml_clear_errors();
    libxml_use_internal_errors($previous);
    $scripts = [];
    foreach ($document->getElementsByTagName('script') as $script) {
        if (!$script->hasAttribute('src')) $scripts[] = $script->textContent;
    }
    $datasets = [];
    foreach ((new DOMXPath($document))->query('//*[@id]') as $element) {
        foreach ($element->attributes as $attribute) {
            if (str_starts_with($attribute->name, 'data-')) {
                $key = preg_replace_callback('/-([a-z])/', fn ($match) => strtoupper($match[1]), substr($attribute->name, 5));
                $datasets[$element->getAttribute('id')][$key] = $attribute->value;
            }
        }
    }
    return compact('scripts', 'datasets');
}

$pages = [];
foreach (['presensi/dashboard', 'presensi/settings', 'presensi/permissions', 'presensi/report', 'mobile_role2/presensi', 'mobile_role2/izin'] as $page) {
    $pageData = $data;
    // Match the real settings controller, which passes the setting model only.
    if ($page === 'presensi/settings') unset($pageData['geofencePolygon']);
    $pages[$page] = renderAttendancePage('backend/' . $page . '.blade.php', $pageData);
    if (in_array($page, ['presensi/dashboard', 'presensi/settings', 'mobile_role2/presensi'], true)) {
        $pageData['setting'] = clone $setting;
        $pageData['setting']->fill(['geofence_mode' => 'radius', 'center_latitude' => -7,
            'center_longitude' => 110, 'radius_meters' => 150]);
        $pageData['schoolGeofence'] = app(App\Services\AttendanceValidationService::class)->getSchoolGeofence($pageData['setting']);
        $pages[$page . ':radius'] = renderAttendancePage('backend/' . $page . '.blade.php', $pageData);
    }
}
echo json_encode(['pages' => $pages, 'message' => $message, 'polygon' => $polygon], JSON_THROW_ON_ERROR);
