<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class AktivasiController extends Controller
{
    public function view()
    {
        $data['title'] = "PERMOHONAN AKTIVASI GURU & PEGAWAI";
        $data['aktivasi'] = DB::select("select * from aktivasi");
        $data['perm_aktivasi'] = DB::table('aktivasi')->where('kelas', request()->user()->kelas_id)->get();
        return view('backend.aktivasi.view', $data);
    }

    public function add()
    {
        $data['title'] = "Input Data Permohonan Aktivasi";
        $data['aktivasi'] = DB::select("select * from aktivasi");
        $data['jenis_surat'] = DB::select("select * from jenis_surat");
        $data['kelas'] = DB::select("select * from kelas");

        return view('backend.aktivasi.add', $data);
    }
    public function addsarpras(Request $request)
    {
        $file_path = public_path() . '/storage/dokumen/aktivasi/' . $request->s_permohonan;
        File::delete($file_path);
        $s_permohonan = $request->file('s_permohonan');
        $filename = $s_permohonan->getClientOriginalName();
        // Simpan ke public_html
        $destinationPath = $_SERVER['DOCUMENT_ROOT'] . '/storage/dokumen/aktivasi/';
        $s_permohonan->move($destinationPath, $filename);
        //$s_permohonan->move(public_path('storage/dokumen/aktivasi'), $filename);
        $data = [
            'id' => $request->id,
            'nama' => $request->nama,
            'kelas' => $request->kelas,
            'tmt_nonaktif' => $request->tmt_nonaktif,
            's_permohonan' => $request->file('s_permohonan')->getClientOriginalName(),
            'status' => "Belum Proses",
            'created_at' => now()
        ];
        // dd($data);
        DB::table('aktivasi')->insert($data);
        Alert::success('Permohonan Aktivasi Berhasil Diajukan!');
        return redirect('aktivasi');
    }
    public function edit(Request $request)
    {
        $data['title'] = "Proses Permohonan Aktivasi";
        $data['aktivasi'] = DB::table('aktivasi')->where('id', $request->id)->first();
        //$data['aktivasi'] = DB::select("select * from aktivasi");
        $data['jenis_surat'] = DB::select("select * from jenis_surat");
        $data['kelas'] = DB::select("select * from kelas");

        return view('backend.aktivasi.edit', $data);
    }
    public function editProses(Request $request)
    {
        $data = [
            'id' => $request->id,
            'nama' => $request->nama,
            'kelas' => $request->kelas,
            'tmt_nonaktif' => $request->tmt_nonaktif,
            'status' => "Proses Selesai",
            'created_at' => now()
        ];
        // dd($data);
        DB::table('aktivasi')->where('id', $request->id)->update($data);
        Alert::success('Permohonan Aktivasi Selesai!');
        return redirect('aktivasi');
    }
    public function delete($id)
    {
        try {
            // dd($id);
            DB::table('aktivasi')->where('id', $id)->delete();
            Alert::success('Data Berhasil Dihapus!');
            return redirect()->route('aktivasi');
        } catch (Exception $e) {
            return response([
                'success' => false,
                'msg'     => 'Error : ' . $e->getMessage() . ' Line : ' . $e->getLine() . ' File : ' . $e->getFile()
            ]);
        }
    }

    // Show edit form for kelengkapan aktivasi (role 1)
    public function editInfo()
    {
        if (request()->user()->role != 1) {
            abort(403);
        }
        $data['title'] = 'Edit Kelengkapan Aktivasi';
        return view('backend.aktivasi.edit_info', $data);
    }

    // Update kelengkapan aktivasi
    public function updateInfo(Request $request)
    {
        if (request()->user()->role != 1) {
            abort(403);
        }
        try {
            $id = $request->id ?? 1;
            $payload = [
                'label_1' => strip_tags($request->input('label_1')) ?? '',
                'label_2' => strip_tags($request->input('label_2')) ?? '',
                'label_3' => strip_tags($request->input('label_3')) ?? '',
                'label_4' => strip_tags($request->input('label_4')) ?? '',
                'ket'     => strip_tags($request->input('ket')) ?? '',
                // optional PDF link for permohonan
                'link_aktivasi' => filter_var($request->input('link_aktivasi'), FILTER_SANITIZE_URL) ?? '',
            ];

            DB::table('aplikasi')->where('id', $id)->update([
                'info_aktivasi' => json_encode($payload),
            ]);

            $params['activity'] = 'Update Kelengkapan Aktivasi';
            $params['detail'] = 'User ' . request()->user()->id . ' updated info_aktivasi';
            \App\Providers\Helper::log_transaction($params);

            Alert::success('Sukses', 'Kelengkapan Aktivasi disimpan');
            return redirect('/aktivasi');
        } catch (Exception $e) {
            return response([
                'success' => false,
                'msg'     => 'Error : ' . $e->getMessage() . ' Line : ' . $e->getLine() . ' File : ' . $e->getFile()
            ]);
        }
    }
}
