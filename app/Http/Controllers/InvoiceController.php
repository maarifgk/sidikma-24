<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InvoiceController extends Controller
{
    /**
     * WAJIB: pastikan semua method di controller ini
     * hanya bisa diakses user yang sudah login
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function view(Request $request)
    {
        $tahunAjaran = $request->tahun_ajaran;

        $data['title'] = "Invoice Iuran LP. Ma'arif NU PCNU Gunungkidul";

        // Data kelas
        $data['kelas'] = DB::table('kelas')->get();

        // List tahun ajaran (dropdown)
        $data['listTahunAjaran'] = DB::table('users')
            ->select('tahun_ajaran')
            ->whereNotNull('tahun_ajaran')
            ->distinct()
            ->orderBy('tahun_ajaran', 'desc')
            ->pluck('tahun_ajaran');

        // Data siswa sesuai tahun ajaran
        $data['datasekolah'] = DB::table('users as u')
            ->leftJoin('kelas as k', 'u.kelas_id', '=', 'k.id')
            ->leftJoin('jurusan as j', 'u.jurusan_id', '=', 'j.id')
            ->where('u.role', 3)
            ->where('u.status', '!=', 'Lulus')
            ->when($tahunAjaran, function ($query) use ($tahunAjaran) {
                $query->where('u.tahun_ajaran', $tahunAjaran);
            })
            ->select('u.*', 'k.nama_kelas', 'j.nama_jurusan')
            ->get();

        $data['tahunTerpilih'] = $tahunAjaran;

        return view('backend.invoice.view', $data);
    }

    public function add($id)
{
    Carbon::setLocale('id');

    $data['title'] = "Invoice Pembayaran";

    // ✅ Ambil data siswa
    $data['siswa'] = DB::table('users')->where('id', $id)->first();
    if (!$data['siswa']) {
        abort(404, 'Data siswa tidak ditemukan');
    }

    // ✅ Pastikan login
    $userId = auth()->id();
    if (!$userId) {
        abort(403, 'Harus login');
    }

    // ✅ Profile user login
    $data['profile'] = DB::table('users')
        ->select(
            'users.*',
            'kelas.nama_kelas',
            'jurusan.nama_jurusan',
            'ketugasan.ketugasan'
        )
        ->leftJoin('kelas', 'kelas.id', '=', 'users.kelas_id')
        ->leftJoin('jurusan', 'jurusan.id', '=', 'users.jurusan_id')
        ->leftJoin('ketugasan', 'ketugasan.id', '=', 'users.ketugasan')
        ->where('users.id', $userId)
        ->first();

    if (!$data['profile']) {
        abort(404, 'Profile user tidak ditemukan');
    }

    // ✅ KUNCI: pastikan kelas_id ada
    if (!$data['siswa']->kelas_id) {
        abort(400, 'Siswa belum memiliki kelas');
    }

    $kelasId = $data['siswa']->kelas_id;

    // ✅ RINGKASAN JUMLAH GURU (AMAN dari NULL)
    $data['gty_nonsertifikasi'] = DB::table('users')
        ->where('role', 2)
        ->whereNotNull('jurusan_id')
        ->where('kelas_id', $kelasId)
        ->whereIn('jurusan_id', [1, 4, 6, 7])
        ->count();

    $data['pns'] = DB::table('users')
        ->where('role', 2)
        ->where('kelas_id', $kelasId)
        ->where('jurusan_id', 5)
        ->count();

    $data['pns_nonsertifikasi'] = DB::table('users')
        ->where('role', 2)
        ->where('kelas_id', $kelasId)
        ->where('jurusan_id', 8)
        ->count();

    $data['gty_sertifikasi'] = DB::table('users')
        ->where('role', 2)
        ->where('kelas_id', $kelasId)
        ->whereIn('jurusan_id', [2, 3])
        ->count();

    return view('backend.invoice.add', $data);
}

}
