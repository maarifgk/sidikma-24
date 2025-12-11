<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InvoiceController extends Controller
{
    public function view(Request $request)
    {
        $tahunAjaran = $request->tahun_ajaran;

        $data['title'] = "Invoice Iuran LP. Ma'arif NU PCNU Gunungkidul";

        // ambil data kelas
        $data['kelas'] = DB::select("SELECT * FROM kelas");

        // daftar tahun ajaran (dropdown)
        // $data['listTahunAjaran'] = DB::table('users')
        //     ->select('tahun_ajaran')
        //     ->distinct()
        //     ->whereNotNull('tahun_ajaran')
        //     ->orderBy('tahun_ajaran', 'desc')
        //     ->pluck('tahun_ajaran');

        // data siswa sesuai tahun pelajaran
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

        // data siswa yg dipilih
        $data['siswa'] = DB::table('users')->where('id', $id)->first();
        if (!$data['siswa']) {
            abort(404);
        }

        // Fetch invoice data for the student
        $data['invoice'] = DB::table('invoices')->where('user_id', $id)->first();

        // If no invoice exists, create default data
        if (!$data['invoice']) {
            $data['invoice'] = (object) [
                'invoice_number' => 'INV-' . date('Y') . '-' . str_pad($id, 3, '0', STR_PAD_LEFT),
                'invoice_date' => now()->format('Y-m-d'),
                'school_name' => 'MI Ma\'arif Wonosari',
                'school_address' => 'Gunungkidul',
                'total_amount' => 1320000.00,
                'notes' => 'Pembayaran iuran dilakukan per semester. Invoice ini sah tanpa tanda tangan. Pembayaran dapat dilakukan melalui transfer bank atau tunai.',
                'status' => 'draft'
            ];
        }

        // profile user login
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
            ->where('users.id', auth()->id())
            ->first();

        $kelasId = $data['siswa']->kelas_id;

        // RINGKASAN JUMLAH GURU
        $data['gty_nonsertifikasi'] = DB::table('users')
            ->where('role', 2)
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
