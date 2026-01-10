<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TagihanController extends Controller
{
    public function view()
    {
        $data['title'] = "Tagihan";
        $data['tagihan'] = DB::select("
            SELECT t.*, k.nama_kelas, ta.tahun, jp.pembayaran, u.nama_lengkap 
            FROM tagihan t 
            LEFT JOIN tahun_ajaran ta ON t.thajaran_id = ta.id 
            LEFT JOIN jenis_pembayaran jp ON jp.id = t.jenis_pembayaran 
            LEFT JOIN users u ON u.id = t.user_id 
            LEFT JOIN kelas k ON k.id = t.kelas_id 
            ORDER BY t.id DESC
        ");
        return view('backend.tagihan.view', $data);
    }
    public function add()
    {
        $data['title'] = "Tagihan";
        $data['siswa'] = DB::select("select * from users where role = '2' or role = '3'");
        $data['kelas'] = DB::select("select * from kelas");
        $data['thajaran'] = DB::select("select * from tahun_ajaran where active = 'ON'");
        $data['jnpembayaran'] = DB::select("select * from jenis_pembayaran where status = 'ON'");
        return view('backend.tagihan.add', $data);
    }
    public function addProses(Request $request)
    {
        foreach ($request->user_id as $k => $u) {
            $dataItem = [
                'user_id' => $u,
                'thajaran_id' => $request->thajaran_id,
                'jenis_pembayaran' => $request->jenis_pembayaran,
                'kelas_id' => $request->kelas_id,
                'keterangan' => $request->keterangan,
                'nilai' => str_replace('.', '', str_replace('Rp. ', '', $request->nilai)),
                'status' => "Belum Lunas",
                'created_at' => now(),
            ];

            // Proses upload jika ada file k_iuran_2024
            if ($request->hasFile('k_iuran_2024')) {
                $file = $request->file('k_iuran_2024');

                $filename = time() . '_' . $file->getClientOriginalName();
                $destinationPath = base_path('../public_html/storage/dokumen/k_iuran_2024');

                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                }

                $file->move($destinationPath, $filename);
                $dataItem['k_iuran_2024'] = $filename;
            }

            $data[] = $dataItem;
        }

        DB::table('tagihan')->insert($data);
        return redirect("tagihan");
    }

    public function jenisPembayaran()
    {
        $data = DB::select("select id, pembayaran as jenis_pembayaran from jenis_pembayaran where status = 'ON'");
        return response()->json($data);
    }
    public function search(Request $request)
    {
        $data['title'] = "Tambah Tagihan";

        if ($request->jenis_pembayaran == 18) {
            // 🔓 Boleh berulang, tampilkan semua siswa
            $data['siswa'] = DB::select("
                SELECT a.id, a.nama_lengkap, a.kelas_id 
                FROM users a
                WHERE (a.role = '2' OR a.role = '3') 
                AND a.kelas_id = '$request->kelas_id'
                ORDER BY a.nama_lengkap
            ");
        } else {
            // 🔒 Settingan awal, tidak boleh double
            $data['siswa'] = DB::select("
                SELECT a.id, a.nama_lengkap, a.kelas_id 
                FROM users a
                WHERE (a.role = '2' OR a.role = '3') 
                AND a.kelas_id = '$request->kelas_id'
                AND a.id NOT IN (
                    SELECT b.user_id FROM tagihan b
                    WHERE b.thajaran_id = '$request->thajaran_id' 
                    AND b.jenis_pembayaran = '$request->jenis_pembayaran' 
                    AND b.kelas_id = '$request->kelas_id'
                )
                ORDER BY a.nama_lengkap
            ");
        }

        $data['bulan'] = DB::select("
            SELECT id, nama_bulan 
            FROM bulan 
            WHERE id NOT IN (
                SELECT bulan_id FROM payment WHERE tagihan_id = '$request'
            )
        ");

        $data['thajaran_id'] = $request->thajaran_id;
        $data['jenis_pembayaran'] = $request->jenis_pembayaran;
        $data['kelas_id'] = $request->kelas_id;

        return view('backend.tagihan.search', $data);
    }

    public function delete($id)
    {
        try {
            // dd($id);
            DB::table('tagihan')->where('id', $id)->delete();
            // Alert::success('Category was successful deleted!');
            return redirect()->route('tagihan');
        } catch (Exception $e) {
            return response([
                'success' => false,
                'msg'     => 'Error : ' . $e->getMessage() . ' Line : ' . $e->getLine() . ' File : ' . $e->getFile()
            ]);
        }
    }

    public function edit($id)
    {
        $data['title'] = "Edit Tagihan";
        $data['tagihan'] = DB::select("
            SELECT t.*, k.nama_kelas, ta.tahun, jp.pembayaran, u.nama_lengkap
            FROM tagihan t
            LEFT JOIN tahun_ajaran ta ON t.thajaran_id = ta.id
            LEFT JOIN jenis_pembayaran jp ON jp.id = t.jenis_pembayaran
            LEFT JOIN users u ON u.id = t.user_id
            LEFT JOIN kelas k ON k.id = t.kelas_id
            WHERE t.id = ?
        ", [$id])[0];

        $data['siswa'] = DB::select("select * from users where role = '2' or role = '3'");
        $data['kelas'] = DB::select("select * from kelas");
        $data['thajaran'] = DB::select("select * from tahun_ajaran where active = 'ON'");
        $data['jnpembayaran'] = DB::select("select * from jenis_pembayaran where status = 'ON'");

        return view('backend.tagihan.edit', $data);
    }

    public function update(Request $request, $id)
    {
        $data = [
            'user_id' => $request->user_id,
            'thajaran_id' => $request->thajaran_id,
            'jenis_pembayaran' => $request->jenis_pembayaran,
            'kelas_id' => $request->kelas_id,
            'keterangan' => $request->keterangan,
            'nilai' => str_replace('.', '', str_replace('Rp. ', '', $request->nilai)),
            'status' => $request->status,
            'updated_at' => now(),
        ];

        // Proses upload jika ada file k_iuran_2024
        if ($request->hasFile('k_iuran_2024')) {
            $file = $request->file('k_iuran_2024');

            $filename = time() . '_' . $file->getClientOriginalName();
            $destinationPath = base_path('../public_html/storage/dokumen/k_iuran_2024');

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            $file->move($destinationPath, $filename);
            $data['k_iuran_2024'] = $filename;
        }

        DB::table('tagihan')->where('id', $id)->update($data);
        return redirect("tagihan");
    }

}
