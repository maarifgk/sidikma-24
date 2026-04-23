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

    protected function ensureRoleThree(): void
    {
        abort_unless(request()->user() && (int) request()->user()->role === 3, 403);
    }

    protected function kelasId()
    {
        return request()->user()->kelas_id;
    }

    protected function usersQuery()
    {
        return DB::table('users')
            ->where('role', 2)
            ->where('kelas_id', $this->kelasId());
    }

    public function dashboard()
    {
        $this->ensureRoleThree();

        $today = today()->toDateString();
        $todayAttendances = Attendance::where('kelas_id', $this->kelasId())
            ->whereDate('attendance_date', $today)
            ->where('status', '!=', 'ditolak');

        $approvedPermissionsToday = AttendancePermission::where('kelas_id', $this->kelasId())
            ->where('status', 'approved')
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today);

        $presentUserCount = (clone $todayAttendances)->distinct('user_id')->count('user_id');
        $permissionUserCount = (clone $approvedPermissionsToday)->distinct('user_id')->count('user_id');
        $totalUsers = $this->usersQuery()->count();

        $chart = collect(range(6, 0))->map(function ($dayOffset) {
            $date = today()->subDays($dayOffset);

            return [
                'date' => $date->format('d/m'),
                'hadir' => Attendance::where('kelas_id', $this->kelasId())
                    ->whereDate('attendance_date', $date)
                    ->whereIn('status', ['hadir', 'terlambat'])
                    ->distinct('user_id')
                    ->count('user_id'),
                'izin' => AttendancePermission::where('kelas_id', $this->kelasId())
                    ->where('status', 'approved')
                    ->whereDate('start_date', '<=', $date)
                    ->whereDate('end_date', '>=', $date)
                    ->count(),
            ];
        });

        $latestActivities = Attendance::query()
            ->select('attendances.*', 'users.nama_lengkap')
            ->leftJoin('users', 'users.id', '=', 'attendances.user_id')
            ->where('attendances.kelas_id', $this->kelasId())
            ->orderByDesc('attendances.checked_at')
            ->limit(8)
            ->get();

        return view('backend.presensi.dashboard', [
            'setting' => $this->validator->settingForKelas($this->kelasId()),
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
        ]);
    }

    public function settings()
    {
        $this->ensureRoleThree();

        return view('backend.presensi.settings', [
            'setting' => $this->validator->settingForKelas($this->kelasId()),
        ]);
    }

    public function updateSettings(Request $request)
    {
        $this->ensureRoleThree();

        $request->validate([
            'check_in_time' => 'required|date_format:H:i',
            'check_out_time' => 'required|date_format:H:i',
            'late_tolerance_minutes' => 'required|integer|min:0|max:240',
            'max_gps_accuracy' => 'required|numeric|min:1|max:100',
            'geofence_polygon' => 'nullable|string',
        ]);

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

        $setting = $this->validator->settingForKelas($this->kelasId());
        $setting->update([
            'enable_check_in' => $request->boolean('enable_check_in'),
            'enable_check_out' => $request->boolean('enable_check_out'),
            'enable_permission' => $request->boolean('enable_permission'),
            'geofence_polygon' => $polygon,
            'check_in_time' => $request->input('check_in_time'),
            'check_out_time' => $request->input('check_out_time'),
            'late_tolerance_minutes' => $request->input('late_tolerance_minutes'),
            'max_gps_accuracy' => $request->input('max_gps_accuracy'),
            'enable_fake_gps_detection' => $request->boolean('enable_fake_gps_detection'),
            'require_selfie' => $request->boolean('require_selfie'),
        ]);

        return redirect()->route('presensi.settings')->with('success', 'Pengaturan presensi berhasil disimpan.');
    }

    public function report(Request $request)
    {
        $this->ensureRoleThree();

        return view('backend.presensi.report', $this->reportData($request));
    }

    public function exportReport(Request $request)
    {
        $this->ensureRoleThree();

        $data = $this->reportData($request);
        $periodLabel = $this->periodLabel($data['filters']['period']);
        $spreadsheet = new Spreadsheet();
        $attendanceSheet = $spreadsheet->getActiveSheet();
        $attendanceSheet->setTitle('Presensi');
        $attendanceSheet->fromArray([
            ['Tanggal', 'Nama', 'Jenis', 'Status', 'Jam', 'Latitude', 'Longitude', 'Akurasi', 'Keterangan'],
        ]);

        $row = 2;
        foreach ($data['attendances'] as $attendance) {
            $attendanceSheet->fromArray([[
                Carbon::parse($attendance->attendance_date)->format('d-m-Y'),
                $attendance->nama_lengkap,
                ucfirst($attendance->check_type),
                ucfirst($attendance->status),
                Carbon::parse($attendance->checked_at)->format('H:i:s'),
                $attendance->latitude,
                $attendance->longitude,
                $attendance->gps_accuracy,
                $attendance->rejection_reason,
            ]], null, 'A' . $row);
            $row++;
        }

        $permissionSheet = $spreadsheet->createSheet();
        $permissionSheet->setTitle('Izin');
        $permissionSheet->fromArray([
            ['Tanggal Mulai', 'Tanggal Selesai', 'Nama', 'Kategori', 'Status', 'Alasan', 'Catatan Review'],
        ]);

        $row = 2;
        foreach ($data['permissions'] as $permission) {
            $permissionSheet->fromArray([[
                Carbon::parse($permission->start_date)->format('d-m-Y'),
                Carbon::parse($permission->end_date)->format('d-m-Y'),
                $permission->nama_lengkap,
                $this->permissionLabel($permission->category),
                ucfirst($permission->status),
                $permission->reason,
                $permission->review_notes,
            ]], null, 'A' . $row);
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
        $this->ensureRoleThree();

        $permissions = AttendancePermission::query()
            ->select('attendance_permissions.*', 'users.nama_lengkap')
            ->leftJoin('users', 'users.id', '=', 'attendance_permissions.user_id')
            ->where('attendance_permissions.kelas_id', $this->kelasId())
            ->orderByDesc('attendance_permissions.created_at')
            ->get();

        return view('backend.presensi.permissions', [
            'permissions' => $permissions,
        ]);
    }

    public function updatePermission(Request $request, $id)
    {
        $this->ensureRoleThree();

        $request->validate([
            'status' => 'required|in:approved,rejected',
            'review_notes' => 'nullable|string|max:2000',
        ]);

        $permission = AttendancePermission::where('kelas_id', $this->kelasId())->findOrFail($id);
        $permission->update([
            'status' => $request->input('status'),
            'review_notes' => $request->input('review_notes'),
            'reviewer_id' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        return redirect()->route('presensi.permissions')->with('success', 'Status izin berhasil diperbarui.');
    }

    protected function reportData(Request $request): array
    {
        $range = $this->resolveReportRange($request);
        $from = $range['from'];
        $to = $range['to'];
        $period = $range['period'];
        $periodDate = $range['periodDate'];
        $userId = $request->input('user_id');
        $status = $request->input('status');

        $attendances = Attendance::query()
            ->select('attendances.*', 'users.nama_lengkap')
            ->leftJoin('users', 'users.id', '=', 'attendances.user_id')
            ->where('attendances.kelas_id', $this->kelasId())
            ->whereBetween('attendances.attendance_date', [$from, $to])
            ->when($userId, fn ($query) => $query->where('attendances.user_id', $userId))
            ->when(in_array($status, ['hadir', 'terlambat', 'ditolak'], true), fn ($query) => $query->where('attendances.status', $status))
            ->when(in_array($status, ['izin', 'cuti'], true), fn ($query) => $query->whereRaw('1 = 0'))
            ->orderByDesc('attendances.checked_at')
            ->get();

        $permissions = AttendancePermission::query()
            ->select('attendance_permissions.*', 'users.nama_lengkap')
            ->leftJoin('users', 'users.id', '=', 'attendance_permissions.user_id')
            ->where('attendance_permissions.kelas_id', $this->kelasId())
            ->whereDate('attendance_permissions.start_date', '<=', $to)
            ->whereDate('attendance_permissions.end_date', '>=', $from)
            ->when($userId, fn ($query) => $query->where('attendance_permissions.user_id', $userId))
            ->when($status === 'izin', fn ($query) => $query->where('attendance_permissions.category', '!=', 'cuti'))
            ->when($status === 'cuti', fn ($query) => $query->where('attendance_permissions.category', 'cuti'))
            ->when(in_array($status, ['hadir', 'terlambat', 'ditolak'], true), fn ($query) => $query->whereRaw('1 = 0'))
            ->orderByDesc('attendance_permissions.created_at')
            ->get();

        return [
            'attendances' => $attendances,
            'permissions' => $permissions,
            'users' => $this->usersQuery()->orderBy('nama_lengkap')->get(),
            'filters' => compact('from', 'to', 'period', 'periodDate', 'userId', 'status'),
        ];
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
