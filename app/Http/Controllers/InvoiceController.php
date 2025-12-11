<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InvoiceController extends Controller
{
    public function view(Request $request)
    {
        $tahunId = $request->tahun_ajaran;

        $data['title'] = "Invoice Pembayaran LP. Ma'arif NU PCNU Gunungkidul";

        // ambil data kelas
        $data['kelas'] = DB::select("SELECT * FROM kelas");

        // daftar tahun ajaran dari database tahun_ajaran (berdasarkan data di tagihan)
        $data['listTahunAjaran'] = DB::table('tahun_ajaran as ta')
            ->whereIn('ta.id', DB::table('tagihan')->distinct()->pluck('thajaran_id'))
            ->orderBy('ta.tahun', 'desc')
            ->get();

        // data siswa sesuai dengan tahun ajaran yang dipilih
        $query = DB::table('users as u')
            ->leftJoin('kelas as k', 'u.kelas_id', '=', 'k.id')
            ->leftJoin('jurusan as j', 'u.jurusan_id', '=', 'j.id')
            ->leftJoin('tagihan as tag', 'u.id', '=', 'tag.user_id')
            ->where('u.role', 3)
            ->where('u.status', '!=', 'Lulus');

        // filter berdasarkan tahun ajaran jika dipilih
        if ($tahunId) {
            $query->where('tag.thajaran_id', $tahunId);
        }

        $data['datasekolah'] = $query
            ->distinct()
            ->select('u.*', 'k.nama_kelas', 'j.nama_jurusan')
            ->get();

        // ambil nama tahun yang dipilih untuk ditampilkan di header
        if ($tahunId) {
            $tahunAjaran = DB::table('tahun_ajaran')->where('id', $tahunId)->first();
            $data['tahunTerpilih'] = $tahunAjaran ? $tahunAjaran->tahun : null;
        } else {
            $data['tahunTerpilih'] = null;
        }

        // hitung total siswa dengan role 2 dari database users
        $data['totalSiswaRole2'] = DB::table('users')
            ->where('role', 2)
            ->where('status', '!=', 'Lulus')
            ->count();

        // hitung pembayaran lunas dan belum lunas dari database tagihan sesuai tahun ajaran
        $tagihanQuery = DB::table('tagihan');

        // jika ada tahun ajaran yang dipilih, filter berdasarkan tahun tersebut
        if ($tahunId) {
            $tagihanQuery->where('thajaran_id', $tahunId);
        }

        $data['totalLunas'] = (clone $tagihanQuery)->where('status', 'Lunas')->count();
        $data['totalBelumLunas'] = (clone $tagihanQuery)->where('status', 'Belum Lunas')->count();

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
                'school_name' => $data['siswa']->nama_lengkap ?? 'MI Ma\'arif Wonosari',
                'school_address' => $data['siswa']->alamat ?? 'Gunungkidul',
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

    public function classBilling($kelasId)
    {
        $data['title'] = 'Tagihan Kelas - Invoice';

        // Get class information
        $data['kelas'] = DB::table('kelas')->where('id', $kelasId)->first();
        if (!$data['kelas']) {
            abort(404);
        }

        // Get all students in this class
        $data['students'] = DB::table('users as u')
            ->leftJoin('kelas as k', 'u.kelas_id', '=', 'k.id')
            ->leftJoin('jurusan as j', 'u.jurusan_id', '=', 'j.id')
            ->where('u.kelas_id', $kelasId)
            ->where('u.role', 3)
            ->where('u.status', '!=', 'Lulus')
            ->select('u.*', 'k.nama_kelas', 'j.nama_jurusan')
            ->get();

        // Get invoices for this class
        $data['invoices'] = DB::table('invoices as i')
            ->join('users as u', 'i.user_id', '=', 'u.id')
            ->where('u.kelas_id', $kelasId)
            ->select('i.*', 'u.nama_lengkap', 'u.name as school_name', 'u.alamat as school_address')
            ->get();

        // Calculate totals
        $data['totalStudents'] = $data['students']->count();
        $data['totalInvoices'] = $data['invoices']->count();
        $data['totalAmount'] = $data['invoices']->sum('total_amount');
        $data['paidInvoices'] = $data['invoices']->where('status', 'paid')->count();
        $data['unpaidInvoices'] = $data['invoices']->where('status', '!=', 'paid')->count();

        return view('backend.invoice.invoice', $data);
    }
}
