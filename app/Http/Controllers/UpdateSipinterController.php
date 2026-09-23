<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Storage;


class UpdateSipinterController extends Controller
{
    public function view()
    {
        $userKelas = request()->user()->kelas_id;

        $data['title'] = "Update Data Sipinter";
        $data['updatesipinter'] = DB::select("select * from updatesipinter");
        $data['sipinter'] = DB::table('updatesipinter')->where('kelas', request()->user()->kelas_id)->get();

        // Cek apakah sudah ada data updatesipinter untuk kelas pengguna
        $data['isDataExist'] = DB::table('updatesipinter')->where('kelas', $userKelas)->exists();

        return view('backend.updatesipinter.view', $data);
    }

    public function add()
    {
        $data['title'] = "Input Data dan Dokumen";
        $data['kelas'] = DB::table('kelas')->get(); // Lebih aman pakai query builder
        return view('backend.updatesipinter.add', $data);
    }

    public function addupdatesipinter(Request $request)
    {
        // Validasi data
        $request->validate([
            'kelas' => 'required|string',
            'npsn' => 'required|string',
            'alamat' => 'required|string',
            'tanah' => 'required|string',
            'status_tanah' => 'required|string',
            'pengelolaakta' => 'required|string',
            'akta' => 'required|string',
            'updatesipinter' => 'required|file|mimes:pdf|max:1048576', // Hanya PDF, maksimal 1 GB
        ]);

        // Cek dan buat folder jika belum ada
        $destinationPath = $_SERVER['DOCUMENT_ROOT'] . '/storage/dokumen/updatesipinter/';

        // Ambil file
        $updatesipinter = $request->file('updatesipinter');
        $filename = time() . '_' . $updatesipinter->getClientOriginalName();

        // Simpan file ke public_html/storage/dokumen/updatesipinter/
        $updatesipinter->move($destinationPath, $filename);

        // Simpan ke database
        DB::table('updatesipinter')->insert([
            'kelas' => $request->kelas,
            'npsn' => $request->npsn,
            'alamat' => $request->alamat,
            'tanah' => $request->tanah,
            'status_tanah' => $request->status_tanah,
            'pengelolaakta' => $request->pengelolaakta,
            'akta' => $request->akta,
            'updatesipinter' => $filename, // Simpan nama file
            'status' => "Dokumen Lengkap",
            'created_at' => now()
        ]);

        Alert::success('Data dan Dokumen Berhasil di Input!');
        return redirect('updatesipinter');
    }

    public function edit(Request $request)
    {
        $data['title'] = "Upload Dokumen";
        $data['updatesipinter'] = DB::table('updatesipinter')->where('id', $request->id)->first();

        return view('backend.updatesipinter.edit', $data);
    }
    public function editProses(Request $request)
    {
        $file_path = public_path() . '/storage/dokumen/pcnu/' . $request->pcnu;
        File::delete($file_path);
        $pcnu = $request->file('pcnu');
        $filename = $pcnu->getClientOriginalName();
        // Simpan ke public_html
        $destinationPath = $_SERVER['DOCUMENT_ROOT'] . '/storage/dokumen/pcnu/';
        $pcnu->move($destinationPath, $filename);
        //$pcnu->move(public_path('storage/dokumen/pcnu'), $filename);
        
        $file_path = public_path() . '/storage/dokumen/pwnu/' . $request->pwnu;
        File::delete($file_path);
        $pwnu = $request->file('pwnu');
        $filename = $pwnu->getClientOriginalName();
        // Simpan ke public_html
        $destinationPath = $_SERVER['DOCUMENT_ROOT'] . '/storage/dokumen/pwnu/';
        $pwnu->move($destinationPath, $filename);
        //$pwnu->move(public_path('storage/dokumen/pwnu'), $filename);

        $data = [
            'pcnu' => $request->file('pcnu')->getClientOriginalName(),
            'pwnu' => $request->file('pwnu')->getClientOriginalName(),
            'updated_at' => now()
        ];
        // dd($data);
        DB::table('updatesipinter')->where('id', $request->id)->update($data);
        Alert::success('Permohonan Persuratan Selesai!');
        return redirect('updatesipinter');
    }
    public function delete($id)
    {
        try {
            // dd($id);
            DB::table('updatesipinter')->where('id', $id)->delete();
            Alert::success('Data Berhasil Dihapus!');
            return redirect()->route('updatesipinter');
        } catch (Exception $e) {
            return response([
                'success' => false,
                'msg'     => 'Error : ' . $e->getMessage() . ' Line : ' . $e->getLine() . ' File : ' . $e->getFile()
            ]);
        }
    }
}
