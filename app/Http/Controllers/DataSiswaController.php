<?php

namespace App\Http\Controllers;

use App\Models\DataSiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Auth;

class DataSiswaController extends Controller
{
    
    public function index(Request $request)
    {
        $user = Auth::user();

        // ✅ Tahun pelajaran aktif (default: terbaru)
        $tahun = $request->get('tahun') ??
            DB::table('tahun_ajaran')->max('tahun');

        // ==========================
        // DATA TABEL
        // ==========================
        $query = DB::table('data_siswa')
            ->join('kelas', 'kelas.id', '=', 'data_siswa.madrasah_id')
            ->select(
                'data_siswa.*',
                'kelas.nama_kelas as nama_madrasah'
            )
            ->where('data_siswa.tahun_pelajaran', $tahun)
            ->orderBy('kelas.nama_kelas');

        // ✅ ROLE 3 hanya lihat madrasah sendiri
        if ($user->role == 3) {
            $query->where('data_siswa.madrasah_id', $user->kelas_id);
        }

        $data = $query->get();

        // ==========================
        // REKAP / SUMMARY
        // ==========================

        // Total semua siswa
        $totalSiswa = DB::table('data_siswa')
            ->where('tahun_pelajaran', $tahun)
            ->sum('total');

        // Total madrasah
        $totalMadrasah = DB::table('kelas')->count();

        // Madrasah yang sudah mengisi (unik per madrasah_id)
        $sudahMengisi = DB::table('data_siswa')
            ->where('tahun_pelajaran', $tahun)
            ->distinct('madrasah_id')
            ->count('madrasah_id');

        // Madrasah yang belum mengisi
        $belumMengisi = $totalMadrasah - $sudahMengisi;

        // List tahun pelajaran (untuk dropdown)
        $listTahun = DB::table('tahun_ajaran')
            ->orderBy('tahun', 'desc')
            ->get();

        return view('backend.data_siswa.index', compact(
            'data',
            'tahun',
            'listTahun',
            'totalSiswa',
            'totalMadrasah',
            'sudahMengisi',
            'belumMengisi'
        ));
    }
   
    public function create()
    {
        $user = Auth::user();

        // Tahun ajaran tetap semua
        $tahun_ajaran = DB::table('tahun_ajaran')
            ->orderBy('tahun', 'desc')
            ->get();

        // ✅ JIKA ROLE = 3 → hanya boleh lihat MADRASAH SENDIRI
        if ($user->role == 3) {
            $madrasah = DB::table('kelas')
                ->where('id', $user->kelas_id)
                ->get();
        } 
        // ✅ ROLE SELAIN 4 → lihat semua
        else {
            $madrasah = DB::table('kelas')
                ->orderBy('nama_kelas')
                ->get();
        }

        return view('backend.data_siswa.create', compact('madrasah', 'tahun_ajaran'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tahun_pelajaran' => 'required',
            'madrasah_id'     => 'required|exists:kelas,id',
        ]);

        $cek = DB::table('data_siswa')
            ->where('madrasah_id', $request->madrasah_id)
            ->where('tahun_pelajaran', $request->tahun_pelajaran)
            ->exists();

        if ($cek) {
            Alert::error(
                'Gagal',
                'Data siswa untuk madrasah dan tahun pelajaran tersebut sudah ada.'
            );
            return redirect()->back()->withInput();
        }

        $total = 0;
        for ($i = 1; $i <= 9; $i++) {
            $total += (int) $request->input("kelas$i", 0);
        }

        DataSiswa::create([
            'madrasah_id'     => $request->madrasah_id,
            'tahun_pelajaran' => $request->tahun_pelajaran,
            'kelas1' => $request->kelas1 ?? 0,
            'kelas2' => $request->kelas2 ?? 0,
            'kelas3' => $request->kelas3 ?? 0,
            'kelas4' => $request->kelas4 ?? 0,
            'kelas5' => $request->kelas5 ?? 0,
            'kelas6' => $request->kelas6 ?? 0,
            'kelas7' => $request->kelas7 ?? 0,
            'kelas8' => $request->kelas8 ?? 0,
            'kelas9' => $request->kelas9 ?? 0,
            'total'  => $total,
        ]);

        Alert::success(
            'Berhasil',
            'Data siswa berhasil ditambahkan'
        );

        return redirect()->route('data-siswa.index');
    }

    public function edit($id)
    {
        $data = DataSiswa::findOrFail($id);

        $madrasah = DB::table('kelas')
            ->orderBy('nama_kelas')
            ->get();

        return view('backend.data_siswa.edit', compact('data','madrasah'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tahun_pelajaran' => 'required',
            'madrasah_id'     => 'required|exists:kelas,id',
        ]);

        $total = 0;
        for ($i = 1; $i <= 9; $i++) {
            $total += (int) $request->input("kelas$i", 0);
        }

        DataSiswa::findOrFail($id)->update([
            'madrasah_id'     => $request->madrasah_id,
            'tahun_pelajaran' => $request->tahun_pelajaran,
            'kelas1' => $request->kelas1 ?? 0,
            'kelas2' => $request->kelas2 ?? 0,
            'kelas3' => $request->kelas3 ?? 0,
            'kelas4' => $request->kelas4 ?? 0,
            'kelas5' => $request->kelas5 ?? 0,
            'kelas6' => $request->kelas6 ?? 0,
            'kelas7' => $request->kelas7 ?? 0,
            'kelas8' => $request->kelas8 ?? 0,
            'kelas9' => $request->kelas9 ?? 0,
            'total'  => $total,
        ]);

        // ✅ SWEETALERT SUCCESS (SAMA SEPERTI CREATE)
        Alert::success(
            'Berhasil',
            'Data siswa berhasil diperbarui'
        );

        return redirect()->route('data-siswa.index');
    }

    public function destroy($id)
    {
        DataSiswa::findOrFail($id)->delete();

        return redirect()
            ->route('data-siswa.index')
            ->with('success','Data berhasil dihapus');
    }
}
