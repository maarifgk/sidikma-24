<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kelas;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use RealRashid\SweetAlert\Facades\Alert;

class UploadSKController extends Controller
{
    // Menampilkan halaman utama Upload SK
    public function index()
    {
        $kelas = DB::select("SELECT * FROM kelas");
        $users = []; // Atau bisa: User::where('kelas_id', 1)->get(); jika ingin default

        return view('backend.upload_sk.index', [
            'title' => 'Upload SK',
            'kelas' => $kelas,
            'users' => $users // tambahkan ini
        ]);
    }

    // AJAX: Ambil data user berdasarkan kelas
    public function getUsers($kelasId)
    {
        $users = User::where('kelas_id', $kelasId)
                    ->select('id', 'nama_lengkap', 'periode', 'sk01_2025') // pastikan periode disertakan
                    ->get();

        return response()->json($users);
    }


    // Simpan file SK yang diupload
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|array',
            'file_sk' => 'required|array',
        ]);

        foreach ($request->user_id as $userId) {
            if ($request->hasFile("file_sk.$userId")) {
                $file = $request->file("file_sk.$userId");

                // Buat nama file unik
                $filename = 'SK_' . $userId . '_' . time() . '.' . $file->getClientOriginalExtension();

                // Simpan ke public_html/uploads/sk
                $destination = public_path('uploads/sk');
                if (!File::exists($destination)) {
                    File::makeDirectory($destination, 0755, true);
                }
                $file->move($destination, $filename);

                // Simpan data ke DB (contoh tabel upload_sk)
                DB::table('upload_sk')->insert([
                    'user_id' => $userId,
                    'file_sk' => 'uploads/sk/' . $filename,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        return redirect()->back()->with('success', 'File SK berhasil diupload.');
    }
    public function uploadSKUser(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'file_sk' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:1048576'
        ]);

        $user = \App\Models\User::find($request->user_id);

        // Ambil nama asli file
        $originalName = $request->file('file_sk')->getClientOriginalName();

        // Tentukan path tujuan: public_html/storage/dokumen/sk01_2025
        $destinationPath = $_SERVER['DOCUMENT_ROOT'] . '/storage/dokumen/sk01_2025';

        // Cek dan buat folder jika belum ada
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0777, true);
        }

        // Pindahkan file ke folder tujuan
        $request->file('file_sk')->move($destinationPath, $originalName);

        // Simpan path relatif ke kolom sk01_2025 (agar bisa ditampilkan di URL nanti)
        $user->sk01_2025 = '' . $originalName;
        $user->save();

        return response()->json(['message' => 'SK berhasil diunggah!']);
    }

}
