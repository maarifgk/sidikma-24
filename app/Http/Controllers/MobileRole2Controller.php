<?php

namespace App\Http\Controllers;

use App\Support\MidtransPaymentSync;
use Illuminate\Support\Facades\DB;

class MobileRole2Controller extends Controller
{
    protected function mobilePaymentBlockedMessage(): string
    {
        return 'Pembayaran mobile tidak dapat dilakukan karena sekolah atau kelas ini masih memiliki tagihan iuran yang belum lunas. Silakan selesaikan iuran terlebih dahulu.';
    }

    protected function hasOutstandingIuranForKelas(?int $kelasId): bool
    {
        if (!$kelasId) {
            return false;
        }

        return DB::table('tagihan')
            ->where('kelas_id', $kelasId)
            ->whereIn('jenis_pembayaran', [14, 16, 19])
            ->where('status', 'Belum Lunas')
            ->exists();
    }

    protected function ensureRoleTwo()
    {
        abort_unless(request()->user() && (int) request()->user()->role === 2, 403);
    }

    protected function profileData()
    {
        return DB::table('users')
            ->select(
                'users.*',
                'kelas.nama_kelas',
                'jurusan.nama_jurusan',
                'ketugasan.ketugasan'
            )
            ->leftJoin('kelas', 'kelas.id', '=', 'users.kelas_id')
            ->leftJoin('jurusan', 'jurusan.id', '=', 'users.jurusan_id')
            ->leftJoin('ketugasan', 'ketugasan.id', '=', 'users.ketugasan')
            ->where('users.id', request()->user()->id)
            ->first();
    }

    protected function teammateQuery()
    {
        return DB::table('users')
            ->select('users.*', 'jurusan.nama_jurusan', 'ketugasan.ketugasan')
            ->leftJoin('jurusan', 'jurusan.id', '=', 'users.jurusan_id')
            ->leftJoin('ketugasan', 'ketugasan.id', '=', 'users.ketugasan')
            ->where('users.role', 2)
            ->where('users.kelas_id', request()->user()->kelas_id)
            ->orderBy('users.nama_lengkap');
    }

    protected function skYayasanDocuments()
    {
        return DB::table('sk_yayasan_documents as docs')
            ->select(
                'docs.id',
                'docs.tahun_sk',
                'docs.original_filename',
                'docs.file_path',
                'docs.source_type',
                'docs.matched_by',
                'templates.name as template_name'
            )
            ->leftJoin('sk_templates as templates', 'templates.id', '=', 'docs.sk_template_id')
            ->where('docs.user_id', request()->user()->id)
            ->orderByDesc('docs.tahun_sk')
            ->orderByDesc('docs.updated_at')
            ->orderByDesc('docs.id')
            ->get();
    }

    protected function skPaymentQuery()
    {
        $latestPayments = DB::table('payment')
            ->select(DB::raw('MAX(id) as last_payment_id'), 'tagihan_id')
            ->groupBy('tagihan_id');

        return DB::table('tagihan as t')
            ->select(
                't.id',
                't.nilai',
                't.status as status_tagihan',
                'ta.tahun',
                'jp.pembayaran',
                'p.status as status_payment',
                'p.pdf_url',
                'p.metode_pembayaran',
                'p.created_at as paid_at'
            )
            ->leftJoin('tahun_ajaran as ta', 'ta.id', '=', 't.thajaran_id')
            ->leftJoin('jenis_pembayaran as jp', 'jp.id', '=', 't.jenis_pembayaran')
            ->leftJoinSub($latestPayments, 'lp', function ($join) {
                $join->on('lp.tagihan_id', '=', 't.id');
            })
            ->leftJoin('payment as p', 'p.id', '=', 'lp.last_payment_id')
            ->where('t.user_id', request()->user()->id)
            ->where('t.jenis_pembayaran', '!=', 1)
            ->where(function ($query) {
                $query->where('jp.pembayaran', 'like', '%SK%')
                    ->orWhere('jp.pembayaran', 'like', '%Yayasan%')
                    ->orWhere('jp.pembayaran', 'like', '%yayasan%');
            })
            ->orderByDesc('t.id');
    }

    public function dashboard()
    {
        $this->ensureRoleTwo();
        MidtransPaymentSync::syncPendingPaymentsForUser((int) request()->user()->id);

        $profile = $this->profileData();
        $teammates = $this->teammateQuery()->limit(5)->get();
        $skPayments = $this->skPaymentQuery()->get();
        $skYayasanDocuments = $this->skYayasanDocuments();

        $data = [
            'pageTitle' => 'Dashboard',
            'activeMenu' => 'dashboard',
            'profile' => $profile,
            'teammates' => $teammates,
            'stats' => [
                'total_rekan' => $this->teammateQuery()->count(),
                'total_bayar' => DB::table('payment')
                    ->where('user_id', request()->user()->id)
                    ->where('status', 'Lunas')
                    ->sum('nilai'),
                'sk_payment_lunas' => $skPayments->where('status_payment', 'Lunas')->count(),
                'sk_file_total' => $skYayasanDocuments->count() + collect([
                    request()->user()->sk01_2025,
                ])->filter()->count(),
            ],
            'recentPayments' => $skPayments->take(3),
            'skYayasanDocuments' => $skYayasanDocuments->take(3),
        ];

        return view('backend.mobile_role2.dashboard', $data);
    }

    public function informasi()
    {
        $this->ensureRoleTwo();

        $profile = $this->profileData();

        $data = [
            'pageTitle' => 'Informasi Guru/Pegawai',
            'activeMenu' => 'informasi',
            'profile' => $profile,
            'teammates' => $this->teammateQuery()->get(),
            'statusCounts' => $this->teammateQuery()
                ->select('jurusan.nama_jurusan', DB::raw('count(users.id) as total'))
                ->groupBy('jurusan.nama_jurusan')
                ->orderByDesc('total')
                ->get(),
        ];

        return view('backend.mobile_role2.informasi', $data);
    }

    public function pembayaran()
    {
        $this->ensureRoleTwo();
        MidtransPaymentSync::syncPendingPaymentsForUser((int) request()->user()->id);

        $profile = $this->profileData();
        $payments = $this->skPaymentQuery()->get();

        $hasUnpaidIuran = $this->hasOutstandingIuranForKelas($profile->kelas_id ?? null);

        $data = [
            'pageTitle' => 'Pembayaran SK Yayasan',
            'activeMenu' => 'pembayaran',
            'profile' => $profile,
            'payments' => $payments,
            'hasUnpaidIuran' => $hasUnpaidIuran,
            'paymentBlockedMessage' => $this->mobilePaymentBlockedMessage(),
            'summary' => [
                'total' => $payments->count(),
                'lunas' => $payments->where('status_payment', 'Lunas')->count(),
                'pending' => $payments->where('status_payment', 'Pending')->count(),
                'nominal' => $payments->sum('nilai'),
            ],
        ];

        return view('backend.mobile_role2.pembayaran', $data);
    }

    public function payment($id_tagihan)
    {
        $this->ensureRoleTwo();
        MidtransPaymentSync::syncPendingPaymentsForTagihan((int) $id_tagihan);

        $payment = DB::table('tagihan as t')
            ->select('t.*', 'u.nama_lengkap', 'jp.pembayaran', 'ta.tahun', 'u.nis', 'u.email', 'u.no_tlp')
            ->leftJoin('users as u', 'u.id', '=', 't.user_id')
            ->leftJoin('jenis_pembayaran as jp', 'jp.id', '=', 't.jenis_pembayaran')
            ->leftJoin('tahun_ajaran as ta', 'ta.id', '=', 't.thajaran_id')
            ->where('t.id', $id_tagihan)
            ->where('t.user_id', request()->user()->id)
            ->get();

        // If no payment found, abort 404
        if ($payment->isEmpty()) {
            abort(404);
        }

        $paymentItem = $payment[0];

        if ($this->hasOutstandingIuranForKelas(request()->user()->kelas_id)) {
            return redirect()->route('mobile.role2.pembayaran')
                ->with('error', $this->mobilePaymentBlockedMessage());
        }

        $data = [
            'pageTitle' => 'Pembayaran',
            'activeMenu' => 'pembayaran',
            'profile' => $this->profileData(),
            'payment' => $payment,
        ];

        return view('backend.mobile_role2.payment', $data);
    }

    public function files()
    {
        $this->ensureRoleTwo();

        $profile = $this->profileData();

        $personalFiles = collect([
            [
                'label' => 'SK User 2025',
                'file' => request()->user()->sk01_2025,
                'path' => request()->user()->sk01_2025 ? asset('storage/dokumen/sk01_2025/' . request()->user()->sk01_2025) : null,
            ],
        ])->filter(fn ($file) => !empty($file['file']))->values();

        $data = [
            'pageTitle' => 'File SK Yayasan',
            'activeMenu' => 'files',
            'profile' => $profile,
            'personalFiles' => $personalFiles,
            'skYayasanDocuments' => $this->skYayasanDocuments(),
        ];

        return view('backend.mobile_role2.files', $data);
    }

    public function profile()
    {
        $this->ensureRoleTwo();
        MidtransPaymentSync::syncPendingPaymentsForUser((int) request()->user()->id);

        $profile = $this->profileData();

        $data = [
            'pageTitle' => 'Profile',
            'activeMenu' => 'profile',
            'profile' => $profile,
            'stats' => [
                'rekan_sekolah' => $this->teammateQuery()->count(),
                'total_pembayaran' => DB::table('payment')
                    ->where('user_id', request()->user()->id)
                    ->where('status', 'Lunas')
                    ->sum('nilai'),
                'tagihan_sk' => $this->skPaymentQuery()->count(),
            ],
        ];

        return view('backend.mobile_role2.profile', $data);
    }
}
