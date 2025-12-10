<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InvoiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function view(Request $request)
    {
        $tahunAjaran = $request->tahun_ajaran;

        $data['title'] = "Invoice Iuran LP. Ma'arif NU PCNU Gunungkidul";

        $data['kelas'] = DB::table('kelas')->get();

        $data['listTahunAjaran'] = DB::table('users')
            ->select('tahun_ajaran')
            ->whereNotNull('tahun_ajaran')
            ->distinct()
            ->orderBy('tahun_ajaran', 'desc')
            ->pluck('tahun_ajaran');

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
    $data = [];
    $data['title'] = 'Invoice';

    $data['siswa'] = DB::table('users')->where('id', $id)->first();
    if (!$data['siswa']) {
        abort(404);
    }

    return view('backend.invoice.add', $data);
}
}
