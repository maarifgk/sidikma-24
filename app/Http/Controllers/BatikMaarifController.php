<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class BatikMaarifController extends Controller
{
    public function index()
    {
        $user = Auth::user(); // ambil user login

        if ($user->role == 1) {
            // ✅ admin lihat semua
            $datasekolah = DB::table('batik_maarif')
                ->join('kelas', 'batik_maarif.asal_sekolah', '=', 'kelas.id')
                ->select('batik_maarif.*', 'kelas.nama_kelas as asal_sekolah')
                ->orderBy('batik_maarif.created_at', 'desc')
                ->get();
        } else {
            // ✅ user biasa lihat sesuai kelas_id
            $datasekolah = DB::table('batik_maarif')
                ->join('kelas', 'batik_maarif.asal_sekolah', '=', 'kelas.id')
                ->select('batik_maarif.*', 'kelas.nama_kelas as asal_sekolah')
                ->where('batik_maarif.asal_sekolah', $user->kelas_id)
                ->orderBy('batik_maarif.created_at', 'desc')
                ->get();
        }

        // di controller index()
        $stok = DB::table('stok_batik')->get();

        return view('backend.batik_maarif.view', [
            'title' => 'Pemesanan Kain Seragam Batik',
            'datasekolah' => $datasekolah,
            // 'kelas' => $kelas,
            'stok' => $stok
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $kelasId = $user->kelas_id;

        $request->validate([
            'produk' => 'required|string',
            'jumlah' => 'required|integer|min:1',
            'harga'  => 'required|integer|min:1',
        ]);

        // ✅ cek stok
        $stok = DB::table('stok_batik')
            ->where('produk', $request->produk)
            ->first();

        if (!$stok || $stok->stok < $request->jumlah) {
            return redirect()->back()->with('error', 'Stok tidak mencukupi!');
        }

        // default nilai pesanan
        $siswa = 0;
        $guru_2m = 0;
        $guru_25m = 0;

        if ($request->produk == 'Batik Siswa MI' || $request->produk == 'Batik Siswa MTs/SMP') {
            $siswa = $request->jumlah;
        } elseif ($request->produk == 'Batik Guru 2 Meter') {
            $guru_2m = $request->jumlah;
        } elseif ($request->produk == 'Batik Guru 2,5 Meter') {
            $guru_25m = $request->jumlah;
        }

        $total_tagihan = $request->jumlah * $request->harga;

        DB::beginTransaction();
        try {
            // ✅ simpan pesanan
            DB::table('batik_maarif')->insert([
                'asal_sekolah'  => $kelasId,
                'siswa'         => $siswa,
                'guru_2m'       => $guru_2m,
                'guru_25m'      => $guru_25m,
                'total_tagihan' => $total_tagihan,
                'keterangan'    => 'belum diambil',
                'created_at'    => now(),
                'updated_at'    => now()
            ]);

            // ✅ kurangi stok
            DB::table('stok_batik')
                ->where('produk', $request->produk)
                ->decrement('stok', $request->jumlah);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menyimpan pesanan!');
        }

        return redirect()->back()->with('success', 'Pesanan berhasil disimpan!');
    }

    public function diterima(Request $request, $id)
    {
        $request->validate([
            'penerima' => 'required|string|max:100'
        ]);

        $update = DB::table('batik_maarif')
            ->where('id', $id)
            ->update([
                'keterangan' => 'sudah diambil',
                'penerima' => $request->penerima,
                'updated_at' => now()
            ]);

        if ($update) {
            return response()->json(['success' => true, 'message' => 'Pesanan berhasil diterima!']);
        }
        return response()->json(['success' => false, 'message' => 'Gagal update data!']);
    }

    public function destroy($id)
    {
        DB::table('batik_maarif')->where('id', $id)->delete();
        return redirect()->back()->with('success', 'Pesanan berhasil dihapus!');
    }

    /**
     * Admin: update stok and harga for a produk
     */
    public function updateStok(Request $request)
    {
        $user = Auth::user();
        if (!$user || $user->role != 1) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $data = $request->only(['produk', 'stok', 'harga']);
        $validator = \Validator::make($data, [
            'produk' => 'required|string',
            'stok' => 'required|integer|min:0',
            'harga' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        try {
            DB::table('stok_batik')->updateOrInsert(
                ['produk' => $data['produk']],
                ['stok' => $data['stok'], 'harga' => $data['harga'], 'updated_at' => now()]
            );
            return response()->json(['success' => true, 'message' => 'Data stok & harga berhasil diperbarui']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menyimpan data: ' . $e->getMessage()], 500);
        }
    }
}
