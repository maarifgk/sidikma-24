<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        if ((int) request()->user()->role === 2) {
            return redirect()->route('mobile.role2.dashboard');
        }

        Carbon::setLocale('id');

        $today = Carbon::now();
        $tahunAjaranStart = $today->month >= 7 ? $today->year : $today->year - 1;
        $tahunAjaranEnd = $tahunAjaranStart + 1;

        $currentTahunAjaran = DB::table('tahun_ajaran')
            ->where('tahun', $tahunAjaranStart)
            ->first();

        if (!$currentTahunAjaran) {
            $currentTahunAjaran = DB::table('tahun_ajaran')
                ->where('active', 'ON')
                ->orderByDesc('id')
                ->first();
        }

        if (!$currentTahunAjaran) {
            $currentTahunAjaran = DB::table('tahun_ajaran')
                ->orderByDesc('id')
                ->first();
        }

        $currentTahunAjaranId = $currentTahunAjaran->id ?? null;
        $currentTahunAjaranLabel = $currentTahunAjaran
            ? ((int) $currentTahunAjaran->tahun) . '/' . ((int) $currentTahunAjaran->tahun + 1)
            : $tahunAjaranStart . '/' . $tahunAjaranEnd;

        $data['rankpayment'] = DB::select(
            "SELECT u.nama_lengkap, p.user_id, k.nama_kelas, u.alamat,  SUM(p.nilai) as total
            FROM payment p
            LEFT JOIN users u on u.id=p.user_id
            LEFT JOIN kelas k on k.id=u.kelas_id
            WHERE p.status = 'Lunas'
            GROUP BY p.user_id, u.nama_lengkap, p.user_id, u.kelas_id, u.alamat
            ORDER BY total DESC LIMIT 7"
        );

        $data['profile'] = DB::table('users')->select('users.*', 'kelas.nama_kelas', 'jurusan.nama_jurusan','ketugasan.ketugasan')
            ->leftJoin('kelas', 'kelas.id', '=', 'users.kelas_id')
            ->leftJoin('jurusan', 'jurusan.id', '=', 'users.jurusan_id')
            ->leftJoin('ketugasan', 'ketugasan.id', '=', 'users.ketugasan')
            ->where('users.id', request()->user()->id)->first();

        $data['temankelas'] = DB::table('users')
            ->where('role', 2)
            ->where('kelas_id', request()->user()->kelas_id)->get();

        $data['totalById'] = request()->user()->role != 1 ?
            DB::table('payment')->where('user_id', request()->user()->id)->sum('nilai') :
            DB::table('payment')->sum('nilai');

        $data['totalBulanan'] = request()->user()->role != 1 ?
            DB::table('payment')->where('user_id', request()->user()->id)->where('bulan_id', '!=', null)->where('status', 'Lunas')->sum('nilai') :
            DB::table('payment')->where('bulan_id', '!=', null)->where('status', 'Lunas')->sum('nilai');

        $data['totalLainya'] = request()->user()->role != 1 ?
            DB::table('payment')->where('user_id', request()->user()->id)->where('bulan_id', '=', null)->where('status', 'Lunas')->sum('nilai') :
            DB::table('payment')->where('bulan_id', '=', null)->where('status', 'Lunas')->sum('nilai');

        $data['kepalasekolah'] = DB::table('users')->where('role', 3)->where('status', 'ON')->count('id');
        $data['kepalasekolahimage'] = DB::table('users')->where('role', 3)->where('status', 'ON')->get();
        $data['total'] = DB::table('users')->where('role', 1)->where('status', 'ON')->count('role');
        $data['img'] = DB::table('users')->where('role', 1)->where('status', 'ON')->get();
        $data['siswatotal'] = DB::table('users')->where('role', 2)->where('status', 'ON')->count('role');
        $data['siswaimg'] = DB::table('users')->where('role', 2)->where('status', 'ON')->get();
        $data['alluserstotal'] = DB::table('users')->where('status', 'ON')->count('role');
        $data['allusersimg'] = DB::table('users')->where('status', 'ON')->get();
        $data['datamadrasah'] = DB::table('users')->where('role', 3)->where('status', 'ON');
        $data['pengurustotal'] = DB::table('users')->where('role', 4)->where('status', 'ON')->count('role');

        $data['datasekolah'] = DB::select("SELECT u.*, k.nama_kelas, j.nama_jurusan
            FROM users u
            LEFT JOIN kelas k ON u.kelas_id = k.id
            LEFT JOIN jurusan j ON u.jurusan_id = j.id
            WHERE role = '3' AND u.status != 'Lulus'");

        $data['siswa'] = DB::select("SELECT u.*, k.nama_kelas, j.nama_jurusan
            FROM users u
            LEFT JOIN kelas k ON u.kelas_id = k.id
            LEFT JOIN jurusan j ON u.jurusan_id = j.id
            WHERE role = '2' AND u.status != 'Lulus'");

        $data['ptt'] = DB::table('users')->where('role', 2)->where('jurusan_id', 7)->count();
        $data['pty'] = DB::table('users')->where('role', 2)->where('jurusan_id', 6)->count();
        $data['pns'] = DB::table('users')->where('role', 2)->where('jurusan_id', 5)->count();
        $data['tidaktetap'] = DB::table('users')->where('role', 2)->where('jurusan_id', 4)->count();
        $data['sudahsertifikasinoninpassing'] = DB::table('users')->where('role', 2)->where('jurusan_id', 3)->count();
        $data['sudahsertifikasi'] = DB::table('users')->where('role', 2)->where('jurusan_id', 2)->count();
        $data['belumsertifikasi'] = DB::table('users')->where('role', 2)->where('jurusan_id', 1)->count();
        $data['tanahbersertifikat'] = DB::table('users')->where('role', 3)->where('sertifikat', 'Sudah Memiliki Sertifikat')->count();
        $data['tanahblmbersertifikat'] = DB::table('users')->where('role', 3)->where('sertifikat', 'Belum Memiliki Sertifikat')->count();

        // ✅ Tambahkan data usulan 5 terbaru
            $data['usulan'] = DB::table('usulan')
            ->select('kelas', 's_pengajuan')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();
        // ✅ Tambahkan data mutasi 5 terbaru
            $data['mutasi'] = DB::table('mutasi')
            ->select('skl_asal', 'skl_tujuan')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();
        // ✅ Tambahkan data aktivasi 5 terbaru
            $data['aktivasi'] = DB::table('aktivasi')
            ->select('kelas', 'status')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();
        // ✅ Tambahkan data persuratan 5 terbaru
            $data['persuratan'] = DB::table('persuratan')
            ->select('kelas', 'status')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();
        // ✅ Tambahkan data proposal 5 terbaru
        $data['proposal'] = DB::table('proposal')
            ->select('kelas_id', 'status')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $paidPaymentsQuery = DB::table('payment as p')
            ->join('tagihan as t', 't.id', '=', 'p.tagihan_id')
            ->where('p.status', 'Lunas')
            ->when($currentTahunAjaranId, function ($query) use ($currentTahunAjaranId) {
                $query->where('t.thajaran_id', $currentTahunAjaranId);
            });

        $data['pendapatan'] = (clone $paidPaymentsQuery)->sum('p.nilai');
        $data['pendapatanTahunLabel'] = $currentTahunAjaranLabel;

        $data['tagihanBelumSelesai'] = DB::table('tagihan')
            ->where('status', 'Belum Lunas')
            ->when($currentTahunAjaranId, function ($query) use ($currentTahunAjaranId) {
                $query->where('thajaran_id', $currentTahunAjaranId);
            })
            ->sum('nilai');
        $data['tagihanTahunLabel'] = $currentTahunAjaranLabel;

        $chartYear = $today->year;
        $chartMonth = $today->month;

        $pendapatanBulanan = (clone $paidPaymentsQuery)
            ->selectRaw('MONTH(p.created_at) as bulan_angka, SUM(p.nilai) as total')
            ->whereNotNull('p.created_at')
            ->whereYear('p.created_at', $chartYear)
            ->whereMonth('p.created_at', '<=', $chartMonth)
            ->groupByRaw('MONTH(p.created_at)')
            ->orderByRaw('MONTH(p.created_at)')
            ->pluck('total', 'bulan_angka');

        $data['grafikPendapatanLabels'] = collect(range(1, $chartMonth))
            ->map(fn ($month) => Carbon::create()->month($month)->translatedFormat('F'))
            ->values()
            ->all();

        $data['grafikPendapatanTotals'] = collect(range(1, $chartMonth))
            ->map(fn ($month) => (int) ($pendapatanBulanan[$month] ?? 0))
            ->values()
            ->all();
        $data['grafikPendapatanYear'] = $chartYear;
        $data['grafikPendapatanMonthLabel'] = Carbon::create()->month($chartMonth)->translatedFormat('F');

        // Ambil 5 pembayaran lunas terbaru pada tahun ajaran aktif
        $data['paymentLatest'] = (clone $paidPaymentsQuery)
            ->select('p.kelas_id', 'p.nilai', 'p.status', 'p.created_at')
            ->orderByDesc('p.created_at')
            ->limit(5)
            ->get()
            ->map(function ($payment) {
                $payment->nilai = (int) preg_replace('/[^\d]/', '', (string) $payment->nilai);

                return $payment;
            });

        $data['grafikPendapatanUpdatedAt'] = now();

        // Data tambahan untuk role 3 (Kepala Sekolah)
        if (request()->user()->role == 3) {
            $data['latest_student_data'] = DB::table('data_siswa')
                ->where('madrasah_id', request()->user()->kelas_id)
                ->orderByDesc('updated_at')
                ->orderByDesc('created_at')
                ->first();

            $data['total_students'] = (int) ($data['latest_student_data']->total ?? 0);
            $data['student_data_year'] = $data['latest_student_data']->tahun_pelajaran ?? null;
            $data['total_teachers'] = DB::table('users')->where('role', 2)->where('kelas_id', request()->user()->kelas_id)->count();
            $data['total_staff'] = DB::table('users')->whereIn('role', [2, 4])->where('kelas_id', request()->user()->kelas_id)->count();
            $data['recent_activities'] = DB::table('usulan')->where('kelas', request()->user()->kelas_id)->orderByDesc('created_at')->limit(5)->get();
        }

        return view('backend.dashboard.index', $data);
    }

    public function open($id)
    {
        // Ambil data pengguna berdasarkan ID
        $user = User::findOrFail($id);
        $data['title'] = "Data Guru/Pegawai";
        $data['periode'] = DB::select("select * from periode");
        $data['siswa'] = DB::table('users')->where('id', $id)->first();
        $data['profile'] = DB::table('users')->select('users.*', 'kelas.nama_kelas', 'jurusan.nama_jurusan','ketugasan.ketugasan')->leftJoin('kelas', 'kelas.id', '=', 'users.kelas_id')->leftJoin('jurusan', 'jurusan.id', '=', 'users.jurusan_id')->leftJoin('ketugasan', 'ketugasan.id', '=', 'users.ketugasan')->where('users.id', request()->user()->id)->first();
        $data['tenagapendidik'] = DB::table('users')
        ->where('role', 2)
        ->where('kelas_id', $user->kelas_id) // Menggunakan $user, bukan $id langsung
        ->where('id', '!=', $user->id) // Hindari menampilkan dirinya sendiri
        ->get();

        $data['ptt'] = DB::table('users')
        ->where('role', 2)
        ->where('kelas_id', $data['siswa']->kelas_id) // Pastikan ambil dari siswa yang dibuka
        ->where('jurusan_id', 7) // Pastikan sesuai dengan siswa yang dibuka
        ->count();
        $data['pty'] = DB::table('users')
        ->where('role', 2)
        ->where('kelas_id', $data['siswa']->kelas_id) // Pastikan ambil dari siswa yang dibuka
        ->where('jurusan_id', 6) // Pastikan sesuai dengan siswa yang dibuka
        ->count();
        $data['gty_nonsertifikasi'] = DB::table('users')
        ->where('role', 2)
        ->where('kelas_id', $data['siswa']->kelas_id) // Pastikan ambil dari siswa yang dibuka
        ->where('jurusan_id', 1) // Pastikan sesuai dengan siswa yang dibuka
        ->count();
        $data['pns'] = DB::table('users')
        ->where('role', 2)
        ->where('kelas_id', $data['siswa']->kelas_id) // Pastikan ambil dari siswa yang dibuka
        ->where('jurusan_id', 5) // Pastikan sesuai dengan siswa yang dibuka
        ->count();
        $data['pns_nonsertifikasi'] = DB::table('users')
        ->where('role', 2)
        ->where('kelas_id', $data['siswa']->kelas_id) // Pastikan ambil dari siswa yang dibuka
        ->where('jurusan_id', 8) // Pastikan sesuai dengan siswa yang dibuka
        ->count();
        $data['gty_sertifikasi_inpassing'] = DB::table('users')
        ->where('role', 2)
        ->where('kelas_id', $data['siswa']->kelas_id) // Pastikan ambil dari siswa yang dibuka
        ->where('jurusan_id', 2) // Pastikan sesuai dengan siswa yang dibuka
        ->count();
        $data['gty_sertifikasi_noninpassing'] = DB::table('users')
        ->where('role', 2)
        ->where('kelas_id', $data['siswa']->kelas_id) // Pastikan ambil dari siswa yang dibuka
        ->where('jurusan_id', 3) // Pastikan sesuai dengan siswa yang dibuka
        ->count();
        $data['gtt'] = DB::table('users')
        ->where('role', 2)
        ->where('kelas_id', $data['siswa']->kelas_id) // Pastikan ambil dari siswa yang dibuka
        ->where('jurusan_id', 4) // Pastikan sesuai dengan siswa yang dibuka
        ->count();
        $data['ketugasanCounts'] = DB::table('users')
        ->join('ketugasan', 'users.ketugasan', '=', 'ketugasan.id')
        ->select('ketugasan.ketugasan', DB::raw('count(users.id) as jumlah'))
        ->where('users.role', 2) // khusus guru/pegawai
        ->where('users.kelas_id', $data['siswa']->kelas_id) // hanya di kelas yg sedang dibuka
        ->groupBy('ketugasan.ketugasan')
        ->get();

        return view('backend.dashboard.open', $data);
    }
}
