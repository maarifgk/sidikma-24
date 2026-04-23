<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendancePermission;
use App\Services\AttendanceValidationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    protected AttendanceValidationService $validator;

    public function __construct(AttendanceValidationService $validator)
    {
        $this->validator = $validator;
    }

    protected function ensureRoleTwo(): void
    {
        abort_unless(request()->user() && (int) request()->user()->role === 2, 403);
    }

    protected function profileData()
    {
        return DB::table('users')
            ->select('users.*', 'kelas.nama_kelas', 'jurusan.nama_jurusan', 'ketugasan.ketugasan')
            ->leftJoin('kelas', 'kelas.id', '=', 'users.kelas_id')
            ->leftJoin('jurusan', 'jurusan.id', '=', 'users.jurusan_id')
            ->leftJoin('ketugasan', 'ketugasan.id', '=', 'users.ketugasan')
            ->where('users.id', request()->user()->id)
            ->first();
    }

    public function index()
    {
        $this->ensureRoleTwo();

        $setting = $this->validator->settingForKelas(request()->user()->kelas_id);
        abort_if(!$setting->enable_check_in && !$setting->enable_check_out, 403);

        $todayAttendances = Attendance::where('user_id', request()->user()->id)
            ->whereDate('attendance_date', today())
            ->where('status', '!=', 'ditolak')
            ->orderBy('checked_at')
            ->get()
            ->keyBy('check_type');

        $history = Attendance::where('user_id', request()->user()->id)
            ->orderByDesc('checked_at')
            ->limit(10)
            ->get();

        return view('backend.mobile_role2.presensi', [
            'pageTitle' => 'Presensi',
            'activeMenu' => 'presensi',
            'profile' => $this->profileData(),
            'setting' => $setting,
            'todayAttendances' => $todayAttendances,
            'history' => $history,
        ]);
    }

    public function store(Request $request)
    {
        $this->ensureRoleTwo();

        $request->validate([
            'check_type' => 'required|in:datang,pulang',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'gps_accuracy' => 'required|numeric',
            'early_checkout_reason' => 'nullable|string|max:1000',
            'selfie' => 'nullable|image|max:4096',
        ]);

        $setting = $this->validator->settingForKelas($request->user()->kelas_id);
        $checkType = $request->input('check_type');

        if ($setting->require_selfie && !$request->hasFile('selfie')) {
            return response()->json([
                'success' => false,
                'message' => 'Foto selfie wajib diunggah untuk presensi.',
            ], 422);
        }

        $alreadyAccepted = Attendance::where('user_id', $request->user()->id)
            ->whereDate('attendance_date', today())
            ->where('check_type', $checkType)
            ->where('status', '!=', 'ditolak')
            ->exists();

        if ($alreadyAccepted) {
            return response()->json([
                'success' => false,
                'message' => 'Presensi ' . $checkType . ' hari ini sudah tercatat.',
            ], 409);
        }

        $result = $this->validator->validateAttendance($request, $setting, $checkType);
        $selfiePath = null;

        if ($request->hasFile('selfie')) {
            $selfiePath = $request->file('selfie')->store('attendance/selfies', 'public');
        }

        Attendance::create([
            'user_id' => $request->user()->id,
            'kelas_id' => $request->user()->kelas_id,
            'attendance_date' => today()->toDateString(),
            'check_type' => $checkType,
            'status' => $result['status'],
            'checked_at' => $result['checked_at'],
            'latitude' => $request->input('latitude'),
            'longitude' => $request->input('longitude'),
            'gps_accuracy' => $request->input('gps_accuracy'),
            'is_inside_geofence' => $result['is_inside_geofence'],
            'is_mock_location' => $result['is_mock_location'],
            'mock_detection_source' => $result['mock_detection_source'],
            'selfie_path' => $selfiePath,
            'rejection_code' => $result['rejection_code'],
            'rejection_reason' => $result['rejection_reason'],
            'device_info' => substr((string) $request->userAgent(), 0, 1000),
        ]);

        return response()->json([
            'success' => $result['accepted'],
            'message' => $result['message'],
            'status' => $result['status'],
        ], $result['accepted'] ? 200 : 422);
    }

    public function permission()
    {
        $this->ensureRoleTwo();

        $setting = $this->validator->settingForKelas(request()->user()->kelas_id);
        abort_if(!$setting->enable_permission, 403);

        $permissions = AttendancePermission::where('user_id', request()->user()->id)
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return view('backend.mobile_role2.izin', [
            'pageTitle' => 'Izin',
            'activeMenu' => 'izin',
            'profile' => $this->profileData(),
            'setting' => $setting,
            'permissions' => $permissions,
        ]);
    }

    public function storePermission(Request $request)
    {
        $this->ensureRoleTwo();

        $setting = $this->validator->settingForKelas($request->user()->kelas_id);
        abort_if(!$setting->enable_permission, 403);

        $request->validate([
            'category' => 'required|in:terlambat,sakit,tidak_masuk,tugas_dinas,cuti',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:2000',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('attendance/permissions', 'public');
        }

        AttendancePermission::create([
            'user_id' => $request->user()->id,
            'kelas_id' => $request->user()->kelas_id,
            'category' => $request->input('category'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'reason' => $request->input('reason'),
            'attachment_path' => $attachmentPath,
            'status' => 'pending',
        ]);

        return redirect()->route('mobile.role2.izin')
            ->with('success', 'Pengajuan izin berhasil dikirim.');
    }
}
