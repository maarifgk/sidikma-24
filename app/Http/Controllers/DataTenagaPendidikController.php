<?php

namespace App\Http\Controllers;

use App\Models\DataTenagaPendidik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Auth;

class DataTenagaPendidikController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $tahun = $request->get('tahun') ?? DB::table('tahun_ajaran')->max('tahun');

        $query = DB::table('data_tenaga_pendidik')
            ->join('kelas', 'kelas.id', '=', 'data_tenaga_pendidik.madrasah_id')
            ->select('data_tenaga_pendidik.*', 'kelas.nama_kelas as nama_madrasah')
            ->where('data_tenaga_pendidik.tahun_pelajaran', $tahun)
            ->orderBy('kelas.nama_kelas');

        if ($user->role == 3) {
            $query->where('data_tenaga_pendidik.madrasah_id', $user->kelas_id);
        }

        $data = $query->get();

        $total = DB::table('data_tenaga_pendidik')
            ->where('tahun_pelajaran', $tahun)
            ->sum('total');

        $totalMadrasah = DB::table('kelas')->count();

        $sudahMengisi = DB::table('data_tenaga_pendidik')
            ->where('tahun_pelajaran', $tahun)
            ->distinct('madrasah_id')
            ->count('madrasah_id');

        $belumMengisi = $totalMadrasah - $sudahMengisi;

        $listTahun = DB::table('tahun_ajaran')
            ->orderBy('tahun', 'desc')
            ->get();

        return view('backend.data_tenaga_pendidik.index', compact(
            'data', 'tahun', 'listTahun', 'total', 'totalMadrasah', 'sudahMengisi', 'belumMengisi'
        ));
    }

    public function create()
    {
        $user = Auth::user();

        $tahun_ajaran = DB::table('tahun_ajaran')
            ->orderBy('tahun', 'desc')
            ->get();

        if ($user->role == 3) {
            $madrasah = DB::table('kelas')
                ->where('id', $user->kelas_id)
                ->get();
        } else {
            $madrasah = DB::table('kelas')
                ->orderBy('nama_kelas')
                ->get();
        }

        return view('backend.data_tenaga_pendidik.create', compact('madrasah', 'tahun_ajaran'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tahun_pelajaran' => 'required',
            'madrasah_id' => 'required|exists:kelas,id',
        ]);

        $exists = DB::table('data_tenaga_pendidik')
            ->where('madrasah_id', $request->madrasah_id)
            ->where('tahun_pelajaran', $request->tahun_pelajaran)
            ->exists();

        if ($exists) {
            Alert::error('Gagal', 'Data tenaga pendidik untuk madrasah dan tahun pelajaran tersebut sudah ada.');
            return redirect()->back()->withInput();
        }

        $fields = [
            'kepala_guru_asn_sertifikasi',
            'kepala_guru_asn_non_sertifikasi',
            'kepala_guru_yayasan_sertifikasi_inpassing',
            'kepala_guru_yayasan_non_sertifikasi',
        ];

        $total = 0;
        foreach ($fields as $f) {
            $total += (int) $request->input($f, 0);
        }

        DataTenagaPendidik::create([
            'madrasah_id' => $request->madrasah_id,
            'tahun_pelajaran' => $request->tahun_pelajaran,
            'kepala_guru_asn_sertifikasi' => $request->kepala_guru_asn_sertifikasi ?? 0,
            'kepala_guru_asn_non_sertifikasi' => $request->kepala_guru_asn_non_sertifikasi ?? 0,
            'kepala_guru_yayasan_sertifikasi_inpassing' => $request->kepala_guru_yayasan_sertifikasi_inpassing ?? 0,
            'kepala_guru_yayasan_non_sertifikasi' => $request->kepala_guru_yayasan_non_sertifikasi ?? 0,
            'total' => $total,
        ]);

        Alert::success('Berhasil', 'Data tenaga pendidik berhasil ditambahkan');

        return redirect()->route('data-tenaga.index');
    }

    public function edit($id)
    {
        $data = DataTenagaPendidik::findOrFail($id);

        $madrasah = DB::table('kelas')
            ->orderBy('nama_kelas')
            ->get();

        return view('backend.data_tenaga_pendidik.edit', compact('data', 'madrasah'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tahun_pelajaran' => 'required',
            'madrasah_id' => 'required|exists:kelas,id',
        ]);

        $fields = [
            'kepala_guru_asn_sertifikasi',
            'kepala_guru_asn_non_sertifikasi',
            'kepala_guru_yayasan_sertifikasi_inpassing',
            'kepala_guru_yayasan_non_sertifikasi',
        ];

        $total = 0;
        foreach ($fields as $f) {
            $total += (int) $request->input($f, 0);
        }

        DataTenagaPendidik::findOrFail($id)->update([
            'madrasah_id' => $request->madrasah_id,
            'tahun_pelajaran' => $request->tahun_pelajaran,
            'kepala_guru_asn_sertifikasi' => $request->kepala_guru_asn_sertifikasi ?? 0,
            'kepala_guru_asn_non_sertifikasi' => $request->kepala_guru_asn_non_sertifikasi ?? 0,
            'kepala_guru_yayasan_sertifikasi_inpassing' => $request->kepala_guru_yayasan_sertifikasi_inpassing ?? 0,
            'kepala_guru_yayasan_non_sertifikasi' => $request->kepala_guru_yayasan_non_sertifikasi ?? 0,
            'total' => $total,
        ]);

        Alert::success('Berhasil', 'Data tenaga pendidik berhasil diperbarui');

        return redirect()->route('data-tenaga.index');
    }

    public function destroy($id)
    {
        DataTenagaPendidik::findOrFail($id)->delete();

        return redirect()->route('data-tenaga.index')->with('success', 'Data berhasil dihapus');
    }
}
