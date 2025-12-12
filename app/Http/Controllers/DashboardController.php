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

            $data['pendapatan2025'] = DB::table('payment')
            ->where('status', 'Lunas')
            ->sum('nilai');

            $data['tagihan2025'] = DB::table('tagihan')
            ->where('status', 'Belum Lunas')
            ->sum('nilai');

            $pendapatanBulanan = DB::table('payment')
                ->selectRaw("MONTHNAME(created_at) as bulan, MONTH(created_at) as bulan_angka, SUM(nilai) as total")
                ->where('status', 'Lunas')
                ->groupByRaw("MONTH(created_at), MONTHNAME(created_at)")
                ->orderByRaw("bulan_angka")
                ->get();

            // Ambil 5 data terbaru dari payment (kelas_id, nilai, status)
            $data['paymentLatest'] = DB::table('payment')
                ->select('kelas_id', 'nilai', 'status')
                ->where('status', 'Lunas')
                ->orderByDesc('created_at')
                ->limit(5)
                ->get();

        $data['grafikPendapatan'] = $pendapatanBulanan;

        // Data tambahan untuk role 3 (Kepala Sekolah)
        if (request()->user()->role == 3) {
            $data['total_students'] = ($data['profile']->kelas1 ?? 0) + ($data['profile']->kelas2 ?? 0) + ($data['profile']->kelas3 ?? 0) + ($data['profile']->kelas4 ?? 0) + ($data['profile']->kelas5 ?? 0) + ($data['profile']->kelas6 ?? 0) + ($data['profile']->kelas7 ?? 0) + ($data['profile']->kelas8 ?? 0) + ($data['profile']->kelas9 ?? 0);
            $data['total_teachers'] = DB::table('users')->where('role', 2)->where('kelas_id', request()->user()->kelas_id)->count();
            $data['total_staff'] = DB::table('users')->whereIn('role', [2, 4])->where('kelas_id', request()->user()->kelas_id)->count();
            $data['recent_activities'] = DB::table('usulan')->where('kelas', request()->user()->kelas_id)->orderByDesc('created_at')->limit(5)->get();
        }

        Carbon::setLocale('id');

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
