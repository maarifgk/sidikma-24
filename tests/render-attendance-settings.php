<?php

// Render the actual settings script for the browser behavior tests, without a DB.
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$setting = new App\Models\AttendanceSetting(['geofence_polygon' => [[-7, 110], [-7, 111], [-6, 111], [-6, 110]]]);
$source = file_get_contents(resource_path('views/backend/presensi/settings.blade.php'));
$section = substr($source, strpos($source, "@section('js')") + strlen("@section('js')"));
$section = substr($section, 0, strrpos($section, '@endsection'));
$compiled = $app['blade.compiler']->compileString($section);
ob_start();
eval('?>' . $compiled);
$html = ob_get_clean();
preg_match_all('~<script>(.*?)</script>~s', $html, $matches);
echo implode("\n", $matches[1]);
