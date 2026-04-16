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

class PersuratanController extends Controller
{
    public function view()
    {
        $data['title'] = "PERMOHONAN PERSURATAN";
        $data['persuratan'] = DB::select("select * from persuratan");
        $data['perm_persuratan'] = DB::table('persuratan')->where('kelas', request()->user()->kelas_id)->get();
        return view('backend.persuratan.view', $data);
    }

    public function add()
    {
        $data['title'] = "Input Data Permohonan Persuratan";
        $data['persuratan'] = DB::select("select * from persuratan");
        $data['jenis_surat'] = DB::select("select * from jenis_surat");
        $data['kelas'] = DB::select("select * from kelas");

        return view('backend.persuratan.add', $data);
    }
    public function addsarpras(Request $request)
    {
        $file_path = public_path() . '/storage/dokumen/persuratan/' . $request->persuratan;
        File::delete($file_path);
        $persuratan = $request->file('persuratan');
        $filename = $persuratan->getClientOriginalName();
        // Simpan ke public_html
        $destinationPath = $_SERVER['DOCUMENT_ROOT'] . '/storage/dokumen/persuratan/';
        $persuratan->move($destinationPath, $filename);
        //$persuratan->move(public_path('storage/dokumen/persuratan'), $filename);
        $data = [
            'id' => $request->id,
            'kelas' => $request->kelas,
            'jenis' => $request->jenis,
            'persuratan' => $request->file('persuratan')->getClientOriginalName(),
            'status' => "Belum Selesai",
            'created_at' => now()
        ];

        // Kirim email ke semua user dengan role 1 dan 4
        $users = \App\Models\User::whereIn('role', [1, 4])->get();

        foreach ($users as $user) {
            \Mail::to($user->email)->send(new \App\Mail\PersuratanSubmitted([
                'kelas' => $request->kelas,
                'jenis' => $request->jenis,
            ]));
        }

        // dd($data);
        DB::table('persuratan')->insert($data);
        Alert::success('Permohonan Persuratan Berhasil Diajukan!');
        return redirect('persuratan');
    }
    public function edit(Request $request)
    {
        $data['title'] = "Proses Permohonan Persuratan";
        $data['persuratan'] = DB::table('persuratan')->where('id', $request->id)->first();
        //$data['persuratan'] = DB::select("select * from persuratan");
        $data['jenis_surat'] = DB::select("select * from jenis_surat");
        $data['kelas'] = DB::select("select * from kelas");

        return view('backend.persuratan.edit', $data);
    }
    public function editProses(Request $request)
    {
        $data = [
            'id' => $request->id,
            'kelas' => $request->kelas,
            'jenis' => $request->jenis,
            'catatan' => $request->catatan,
            'status' => "Selesai",
            'created_at' => now()
        ];

        // Jika ada file yang diupload
        if ($request->hasFile('surat_acc')) {
            // Hapus file lama (jika ada)
            $file_path = public_path('storage/dokumen/surat_acc/' . $request->surat_acc_lama);
            if (File::exists($file_path)) {
                File::delete($file_path);
            }

            // Simpan file baru
            $surat_acc = $request->file('surat_acc');
            $filename = $surat_acc->getClientOriginalName();

            $destinationPath = public_path('storage/dokumen/surat_acc/');
            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true);
            }

            $surat_acc->move($destinationPath, $filename);

            // Masukkan nama file ke dalam data yang akan diupdate
            $data['surat_acc'] = $filename;
        }

        // Update ke database
        DB::table('persuratan')->where('id', $request->id)->update($data);

        Alert::success('Permohonan Persuratan Selesai!');
        return redirect('persuratan');
    }

    public function delete($id)
    {
        try {
            // dd($id);
            DB::table('persuratan')->where('id', $id)->delete();
            Alert::success('Data Berhasil Dihapus!');
            return redirect()->route('persuratan');
        } catch (Exception $e) {
            return response([
                'success' => false,
                'msg'     => 'Error : ' . $e->getMessage() . ' Line : ' . $e->getLine() . ' File : ' . $e->getFile()
            ]);
        }
    }

    // Show edit form for kelengkapan persuratan (role 1)
    public function editInfo()
    {
        if (request()->user()->role != 1) {
            abort(403);
        }
        $data['title'] = 'Edit Kelengkapan Persuratan';
        return view('backend.persuratan.edit_info', $data);
    }

    // Update kelengkapan persuratan
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
                'link_persuratan' => filter_var($request->input('link_persuratan'), FILTER_SANITIZE_URL) ?? '',
            ];

            DB::table('aplikasi')->where('id', $id)->update([
                'info_persuratan' => json_encode($payload),
            ]);

            $params['activity'] = 'Update Kelengkapan Persuratan';
            $params['detail'] = 'User ' . request()->user()->id . ' updated info_persuratan';
            \App\Providers\Helper::log_transaction($params);

            Alert::success('Sukses', 'Kelengkapan Persuratan disimpan');
            return redirect('/persuratan');
        } catch (Exception $e) {
            return response([
                'success' => false,
                'msg'     => 'Error : ' . $e->getMessage() . ' Line : ' . $e->getLine() . ' File : ' . $e->getFile()
            ]);
        }
    }
}
