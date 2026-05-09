<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendancePermission;
use App\Services\AttendanceValidationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class AttendanceAdminController extends Controller
{
    protected AttendanceValidationService $validator;

    public function __construct(AttendanceValidationService $validator)
    {
        $this->validator = $validator;
    }

    protected function ensurePresensiAdmin(): void
    {
        abort_unless(request()->user() && in_array((int) request()->user()->role, [1, 3], true), 403);
    }

    protected function isRoleOne(): bool
    {
        return request()->user() && (int) request()->user()->role === 1;
    }

    protected function kelasId(): ?int
    {
        return request()->user() ? (int) request()->user()->kelas_id : null;
    }

    protected function selectedKelasId(?Request $request = null, bool $fallbackToFirstForRoleOne = false): ?int
    {
        $request ??= request();

        if (!$this->isRoleOne()) {
            return $this->kelasId();
        }

        if ($request->filled('kelas_id')) {
            $kelasId = (int) $request->input('kelas_id');

            if (DB::table('kelas')->where('id', $kelasId)->exists()) {
                return $kelasId;
            }
        }

        if ($fallbackToFirstForRoleOne) {
            $kelasId = DB::table('kelas')->orderBy('nama_kelas')->value('id');

            return $kelasId ? (int) $kelasId : null;
        }

        return null;
    }

    protected function applyKelasScope($query, string $column, ?int $kelasId = null)
    {
        $resolvedKelasId = $this->isRoleOne() ? $kelasId : $this->kelasId();

        if ($resolvedKelasId) {
            $query->where($column, $resolvedKelasId);
        }

        return $query;
    }

    protected function kelasOptions()
    {
        return DB::table('kelas')
            ->select('id', 'nama_kelas')
            ->orderBy('nama_kelas')
            ->get();
    }

    protected function selectedKelasName(?int $kelasId): ?string
    {
        if (!$kelasId) {
            return null;
        }

        return DB::table('kelas')->where('id', $kelasId)->value('nama_kelas');
    }

    protected function filtersMeta(?int $selectedKelasId): array
    {
        return [
            'canSelectKelas' => $this->isRoleOne(),
            'classes' => $this->isRoleOne() ? $this->kelasOptions() : collect(),
            'selectedKelasId' => $selectedKelasId,
            'selectedKelasName' => $this->selectedKelasName($selectedKelasId),
        ];
    }

    protected function usersQuery(?int $kelasId = null)
    {
        $query = DB::table('users')
            ->select('users.*', 'kelas.nama_kelas')
            ->leftJoin('kelas', 'kelas.id', '=', 'users.kelas_id')
            ->where('users.role', 2);

        return $this->applyKelasScope($query, 'users.kelas_id', $kelasId);
    }

    public function dashboard()
    {
        $this->ensurePresensiAdmin();

        $today = today()->toDateString();
        $selectedKelasId = $this->selectedKelasId();
        $setting = $selectedKelasId ? $this->validator->settingForKelas($selectedKelasId) : null;

        $todayAttendances = $this->applyKelasScope(Attendance::query(), 'kelas_id', $selectedKelasId)
            ->whereDate('attendance_date', $today)
            ->where('status', '!=', 'ditolak');

        $approvedPermissionsToday = $this->applyKelasScope(AttendancePermission::query(), 'kelas_id', $selectedKelasId)
            ->where('status', 'approved')
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today);

        $presentUserCount = (clone $todayAttendances)->distinct('user_id')->count('user_id');
        $permissionUserCount = (clone $approvedPermissionsToday)->distinct('user_id')->count('user_id');
        $totalUsers = $this->usersQuery($selectedKelasId)->count();

        $chart = collect(range(6, 0))->map(function ($dayOffset) use ($selectedKelasId) {
            $date = today()->subDays($dayOffset);

            return [
                'date' => $date->format('d/m'),
                'hadir' => $this->applyKelasScope(Attendance::query(), 'kelas_id', $selectedKelasId)
                    ->whereDate('attendance_date', $date)
                    ->whereIn('status', ['hadir', 'terlambat'])
                    ->distinct('user_id')
                    ->count('user_id'),
                'izin' => $this->applyKelasScope(AttendancePermission::query(), 'kelas_id', $selectedKelasId)
                    ->where('status', 'approved')
                    ->whereDate('start_date', '<=', $date)
                    ->whereDate('end_date', '>=', $date)
                    ->count(),
            ];
        });

        $latestActivities = $this->applyKelasScope(Attendance::query(), 'attendances.kelas_id', $selectedKelasId)
            ->select('attendances.*', 'users.nama_lengkap', 'kelas.nama_kelas')
            ->leftJoin('users', 'users.id', '=', 'attendances.user_id')
            ->leftJoin('kelas', 'kelas.id', '=', 'attendances.kelas_id')
            ->orderByRaw('COALESCE(attendances.check_out_at, attendances.check_in_at, attendances.checked_at) DESC')
            ->limit(8)
            ->get();

        $attendanceMapPoints = $this->applyKelasScope(Attendance::query(), 'attendances.kelas_id', $selectedKelasId)
            ->select('attendances.*', 'users.nama_lengkap', 'kelas.nama_kelas')
            ->leftJoin('users', 'users.id', '=', 'attendances.user_id')
            ->leftJoin('kelas', 'kelas.id', '=', 'attendances.kelas_id')
            ->whereDate('attendances.attendance_date', $today)
            ->where(function ($query) {
                $query
                    ->whereNotNull('attendances.check_out_latitude')
                    ->whereNotNull('attendances.check_out_longitude')
                    ->orWhere(function ($subQuery) {
                        $subQuery
                            ->whereNotNull('attendances.check_in_latitude')
                            ->whereNotNull('attendances.check_in_longitude');
                    })
                    ->orWhere(function ($subQuery) {
                        $subQuery
                            ->whereNotNull('attendances.latitude')
                            ->whereNotNull('attendances.longitude');
                    });
            })
            ->orderByRaw('COALESCE(attendances.check_out_at, attendances.check_in_at, attendances.checked_at) DESC')
            ->get()
            ->unique('user_id')
            ->values()
            ->map(function (Attendance $attendance) {
                $latitude = $attendance->check_out_latitude
                    ?? $attendance->check_in_latitude
                    ?? $attendance->latitude;
                $longitude = $attendance->check_out_longitude
                    ?? $attendance->check_in_longitude
                    ?? $attendance->longitude;
                $gpsAccuracy = $attendance->check_out_gps_accuracy
                    ?? $attendance->check_in_gps_accuracy
                    ?? $attendance->gps_accuracy;
                $checkedAt = $attendance->check_out_at
                    ?? $attendance->check_in_at
                    ?? $attendance->checked_at;
                $checkLabel = $attendance->check_out_at
                    ? 'Presensi pulang'
                    : ($attendance->check_in_at || $attendance->check_type === 'datang' ? 'Presensi datang' : 'Presensi');

                return [
                    'user_id' => $attendance->user_id,
                    'name' => $attendance->nama_lengkap ?? '-',
                    'school_name' => $attendance->nama_kelas ?? '-',
                    'status' => $attendance->status,
                    'check_label' => $checkLabel,
                    'checked_at' => $checkedAt ? $checkedAt->format('H:i') : '-',
                    'latitude' => (float) $latitude,
                    'longitude' => (float) $longitude,
                    'gps_accuracy' => $gpsAccuracy !== null ? (float) $gpsAccuracy : null,
                ];
            });

        return view('backend.presensi.dashboard', array_merge([
            'stats' => [
                'total_hadir' => $presentUserCount,
                'hadir' => (clone $todayAttendances)->where('status', 'hadir')->distinct('user_id')->count('user_id'),
                'terlambat' => (clone $todayAttendances)->where('status', 'terlambat')->distinct('user_id')->count('user_id'),
                'izin' => (clone $approvedPermissionsToday)->whereIn('category', ['terlambat', 'sakit', 'tidak_masuk', 'tugas_dinas'])->count(),
                'cuti' => (clone $approvedPermissionsToday)->where('category', 'cuti')->count(),
                'tidak_hadir' => max($totalUsers - $presentUserCount - $permissionUserCount, 0),
                'persentase' => $totalUsers > 0 ? round(($presentUserCount / $totalUsers) * 100, 1) : 0,
            ],
            'chart' => $chart,
            'latestActivities' => $latestActivities,
            'attendanceMapPoints' => $attendanceMapPoints,
            'geofencePolygon' => $setting ? $this->validator->normalizePolygon($setting->geofence_polygon) : [],
        ], $this->filtersMeta($selectedKelasId)));
    }

    public function settings()
    {
        $this->ensurePresensiAdmin();

        $selectedKelasId = $this->selectedKelasId(request(), true);

        return view('backend.presensi.settings', array_merge([
            'setting' => $this->validator->settingForKelas($selectedKelasId),
        ], $this->filtersMeta($selectedKelasId)));
    }

    public function updateSettings(Request $request)
    {
        $this->ensurePresensiAdmin();

        $request->validate([
            'kelas_id' => $this->isRoleOne() ? 'required|integer|exists:kelas,id' : 'nullable',
            'check_in_time' => 'required|date_format:H:i',
            'check_out_time' => 'required|date_format:H:i',
            'late_tolerance_minutes' => 'required|integer|min:0|max:240',
            'max_gps_accuracy' => 'nullable|numeric|min:1|max:100',
            'geofence_polygon' => 'nullable|string',
        ]);

        $selectedKelasId = $this->selectedKelasId($request, true);

        $maxGpsAccuracy = $request->filled('max_gps_accuracy')
            ? $request->input('max_gps_accuracy')
            : 2;

        $polygon = null;
        if ($request->filled('geofence_polygon')) {
            $decodedPolygon = json_decode($request->input('geofence_polygon'), true);
            $normalizedPolygon = $this->validator->normalizePolygon($decodedPolygon);

            if (count($normalizedPolygon) < 3) {
                return back()->withInput()->withErrors([
                    'geofence_polygon' => 'Polygon harus berupa JSON berisi minimal 3 titik lat/lng.',
                ]);
            }

            $polygon = $normalizedPolygon;
        }

        $setting = $this->validator->settingForKelas($selectedKelasId);
        $setting->update([
            'enable_check_in' => $request->boolean('enable_check_in'),
            'enable_check_out' => $request->boolean('enable_check_out'),
            'enable_permission' => $request->boolean('enable_permission'),
            'geofence_polygon' => $polygon,
            'check_in_time' => $request->input('check_in_time'),
            'check_out_time' => $request->input('check_out_time'),
            'late_tolerance_minutes' => $request->input('late_tolerance_minutes'),
            'max_gps_accuracy' => $maxGpsAccuracy,
            'enable_fake_gps_detection' => $request->boolean('enable_fake_gps_detection'),
            'require_selfie' => $request->boolean('require_selfie'),
        ]);

        return redirect()->route('presensi.settings', ['kelas_id' => $selectedKelasId])
            ->with('success', 'Pengaturan presensi berhasil disimpan.');
    }

    public function report(Request $request)
    {
        $this->ensurePresensiAdmin();

        return view('backend.presensi.report', $this->reportData($request));
    }

    public function exportReport(Request $request)
    {
        $this->ensurePresensiAdmin();

        $data = $this->reportData($request);
        $periodLabel = $this->periodLabel($data['filters']['period']);
        $spreadsheet = new Spreadsheet();
        $attendanceSheet = $spreadsheet->getActiveSheet();
        $attendanceSheet->setTitle('Presensi');
        $attendanceHeaders = ['Tanggal'];
        if ($this->isRoleOne()) {
            $attendanceHeaders[] = 'Sekolah';
        }
        $attendanceHeaders = array_merge($attendanceHeaders, ['Nama', 'Status Masuk', 'Jam Masuk', 'Jam Pulang', 'Lokasi Masuk', 'Lokasi Pulang', 'Keterangan']);
        $attendanceSheet->fromArray([$attendanceHeaders]);

        $row = 2;
        foreach ($data['attendances'] as $attendance) {
            $rowData = [
                Carbon::parse($attendance->attendance_date)->format('d-m-Y'),
            ];
            if ($this->isRoleOne()) {
                $rowData[] = $attendance->nama_kelas;
            }
            $rowData = array_merge($rowData, [
                $attendance->nama_lengkap,
                ucfirst($attendance->status),
                $attendance->check_in_time ? $attendance->check_in_time->format('H:i:s') : '-',
                $attendance->check_out_time ? $attendance->check_out_time->format('H:i:s') : '-',
                $this->formatAttendanceLocation($attendance, 'check_in'),
                $this->formatAttendanceLocation($attendance, 'check_out'),
                $attendance->combined_note,
            ]);
            $attendanceSheet->fromArray([$rowData], null, 'A' . $row);
            $row++;
        }

        $permissionSheet = $spreadsheet->createSheet();
        $permissionSheet->setTitle('Izin');
        $permissionHeaders = ['Tanggal Mulai', 'Tanggal Selesai'];
        if ($this->isRoleOne()) {
            $permissionHeaders[] = 'Sekolah';
        }
        $permissionHeaders = array_merge($permissionHeaders, ['Nama', 'Kategori', 'Status', 'Alasan', 'Catatan Review']);
        $permissionSheet->fromArray([$permissionHeaders]);

        $row = 2;
        foreach ($data['permissions'] as $permission) {
            $rowData = [
                Carbon::parse($permission->start_date)->format('d-m-Y'),
                Carbon::parse($permission->end_date)->format('d-m-Y'),
            ];
            if ($this->isRoleOne()) {
                $rowData[] = $permission->nama_kelas;
            }
            $rowData = array_merge($rowData, [
                $permission->nama_lengkap,
                $this->permissionLabel($permission->category),
                ucfirst($permission->status),
                $permission->reason,
                $permission->review_notes,
            ]);
            $permissionSheet->fromArray([$rowData], null, 'A' . $row);
            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'laporan-presensi-' . $periodLabel . '-' . $data['filters']['from'] . '-sd-' . $data['filters']['to'] . '.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function permissions()
    {
        $this->ensurePresensiAdmin();

        $status = request()->input('status', 'pending');
        if (!in_array($status, ['pending', 'approved', 'rejected', 'all'], true)) {
            $status = 'pending';
        }

        $selectedKelasId = $this->selectedKelasId();
        $baseQuery = $this->applyKelasScope(AttendancePermission::query(), 'kelas_id', $selectedKelasId);

        $permissions = $this->applyKelasScope(AttendancePermission::query(), 'attendance_permissions.kelas_id', $selectedKelasId)
            ->select('attendance_permissions.*', 'users.nama_lengkap', 'reviewer.nama_lengkap as reviewer_name', 'kelas.nama_kelas')
            ->leftJoin('users', 'users.id', '=', 'attendance_permissions.user_id')
            ->leftJoin('users as reviewer', 'reviewer.id', '=', 'attendance_permissions.reviewer_id')
            ->leftJoin('kelas', 'kelas.id', '=', 'attendance_permissions.kelas_id')
            ->when($status !== 'all', fn ($query) => $query->where('attendance_permissions.status', $status))
            ->orderByDesc('attendance_permissions.created_at')
            ->get();

        return view('backend.presensi.permissions', array_merge([
            'permissions' => $permissions,
            'status' => $status,
            'filters' => ['kelasId' => $selectedKelasId],
            'summary' => [
                'pending' => (clone $baseQuery)->where('status', 'pending')->count(),
                'approved' => (clone $baseQuery)->where('status', 'approved')->count(),
                'rejected' => (clone $baseQuery)->where('status', 'rejected')->count(),
                'all' => (clone $baseQuery)->count(),
            ],
        ], $this->filtersMeta($selectedKelasId)));
    }

    public function updatePermission(Request $request, $id)
    {
        $this->ensurePresensiAdmin();

        $request->validate([
            'kelas_id' => $this->isRoleOne() ? 'nullable|integer|exists:kelas,id' : 'nullable',
            'status' => 'required|in:approved,rejected',
            'review_notes' => 'nullable|string|max:2000',
        ]);

        $selectedKelasId = $this->selectedKelasId($request);
        $permissionQuery = AttendancePermission::query();
        $this->applyKelasScope($permissionQuery, 'kelas_id', $selectedKelasId);
        $permission = $permissionQuery->findOrFail($id);
        $permission->update([
            'status' => $request->input('status'),
            'review_notes' => $request->input('review_notes'),
            'reviewer_id' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        $message = $request->input('status') === 'approved'
            ? 'Pengajuan izin berhasil disetujui.'
            : 'Pengajuan izin berhasil ditolak.';

        return redirect()->route('presensi.permissions', [
            'status' => $request->input('status'),
            'kelas_id' => $selectedKelasId,
        ])
            ->with('success', $message);
    }

    protected function reportData(Request $request): array
    {
        $selectedKelasId = $this->selectedKelasId($request);
        $range = $this->resolveReportRange($request);
        $from = $range['from'];
        $to = $range['to'];
        $period = $range['period'];
        $periodDate = $range['periodDate'];
        $userId = $request->input('user_id');
        $status = $request->input('status');

        $attendances = $this->applyKelasScope(Attendance::query(), 'attendances.kelas_id', $selectedKelasId)
            ->select('attendances.*', 'users.nama_lengkap', 'kelas.nama_kelas')
            ->leftJoin('users', 'users.id', '=', 'attendances.user_id')
            ->leftJoin('kelas', 'kelas.id', '=', 'attendances.kelas_id')
            ->whereBetween('attendances.attendance_date', [$from, $to])
            ->when($userId, fn ($query) => $query->where('attendances.user_id', $userId))
            ->when(in_array($status, ['hadir', 'terlambat', 'ditolak'], true), fn ($query) => $query->where('attendances.status', $status))
            ->when(in_array($status, ['izin', 'cuti'], true), fn ($query) => $query->whereRaw('1 = 0'))
            ->orderByRaw('COALESCE(attendances.check_out_at, attendances.check_in_at, attendances.checked_at) DESC')
            ->get();

        $permissions = $this->applyKelasScope(AttendancePermission::query(), 'attendance_permissions.kelas_id', $selectedKelasId)
            ->select('attendance_permissions.*', 'users.nama_lengkap', 'kelas.nama_kelas')
            ->leftJoin('users', 'users.id', '=', 'attendance_permissions.user_id')
            ->leftJoin('kelas', 'kelas.id', '=', 'attendance_permissions.kelas_id')
            ->whereDate('attendance_permissions.start_date', '<=', $to)
            ->whereDate('attendance_permissions.end_date', '>=', $from)
            ->when($userId, fn ($query) => $query->where('attendance_permissions.user_id', $userId))
            ->when($status === 'izin', fn ($query) => $query->where('attendance_permissions.category', '!=', 'cuti'))
            ->when($status === 'cuti', fn ($query) => $query->where('attendance_permissions.category', 'cuti'))
            ->when(in_array($status, ['hadir', 'terlambat', 'ditolak'], true), fn ($query) => $query->whereRaw('1 = 0'))
            ->orderByDesc('attendance_permissions.created_at')
            ->get();

        return array_merge([
            'attendances' => $attendances,
            'permissions' => $permissions,
            'users' => $this->usersQuery($selectedKelasId)->orderBy('users.nama_lengkap')->get(),
            'filters' => compact('from', 'to', 'period', 'periodDate', 'userId', 'status') + ['kelasId' => $selectedKelasId],
        ], $this->filtersMeta($selectedKelasId));
    }

    protected function resolveReportRange(Request $request): array
    {
        $period = $request->input('period', 'custom');
        if (!in_array($period, ['harian', 'mingguan', 'bulanan', 'custom'], true)) {
            $period = 'custom';
        }

        $periodDate = $request->input('period_date', $request->input('from', today()->toDateString()));
        $anchorDate = Carbon::parse($periodDate ?: today()->toDateString());

        if ($period === 'harian') {
            $from = $anchorDate->copy()->toDateString();
            $to = $anchorDate->copy()->toDateString();
        } elseif ($period === 'mingguan') {
            $from = $anchorDate->copy()->startOfWeek(Carbon::MONDAY)->toDateString();
            $to = $anchorDate->copy()->endOfWeek(Carbon::SUNDAY)->toDateString();
        } elseif ($period === 'bulanan') {
            $from = $anchorDate->copy()->startOfMonth()->toDateString();
            $to = $anchorDate->copy()->endOfMonth()->toDateString();
        } else {
            $from = $request->input('from', today()->startOfMonth()->toDateString());
            $to = $request->input('to', today()->toDateString());
            $periodDate = $from;
        }

        return compact('from', 'to', 'period', 'periodDate');
    }

    protected function formatAttendanceLocation(Attendance $attendance, string $prefix): string
    {
        $latitude = $attendance->{$prefix . '_latitude'};
        $longitude = $attendance->{$prefix . '_longitude'};

        if ($latitude === null || $longitude === null) {
            if ($prefix === 'check_in' && $attendance->check_type === 'datang') {
                $latitude = $attendance->latitude;
                $longitude = $attendance->longitude;
            }

            if ($prefix === 'check_out' && $attendance->check_type === 'pulang') {
                $latitude = $attendance->latitude;
                $longitude = $attendance->longitude;
            }
        }

        return $latitude !== null && $longitude !== null
            ? $latitude . ', ' . $longitude
            : '-';
    }

    protected function periodLabel(string $period): string
    {
        return [
            'harian' => 'harian',
            'mingguan' => 'mingguan',
            'bulanan' => 'bulanan',
            'custom' => 'kustom',
        ][$period] ?? 'kustom';
    }

    protected function permissionLabel(string $category): string
    {
        return [
            'terlambat' => 'Izin Terlambat',
            'sakit' => 'Izin Sakit',
            'tidak_masuk' => 'Izin Tidak Masuk',
            'tugas_dinas' => 'Izin Tugas Dinas',
            'cuti' => 'Izin Cuti',
        ][$category] ?? $category;
    }
}
