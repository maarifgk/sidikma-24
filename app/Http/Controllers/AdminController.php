<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use RealRashid\SweetAlert\Facades\Alert;

class AdminController extends Controller
{
    public function index()
    {
        $data['title'] = "Admin";
        $data['admin'] = DB::select("select * from users where role != '2'");
        return view('backend.admin.index', $data);
    }
    public function add()
    {
        $data['title'] = "Tambah Admin";
        $data['kelas'] = DB::select("select * from kelas");
        $data['admin'] = DB::table('users');
        return view('backend.admin.add', $data);
    }
    public function addProses(Request $request)
    {
        $file_path = public_path() . '/storage/images/users/' . $request->image;
        File::delete($file_path);
        $image = $request->file('image');
        //$filename = $image->getClientOriginalName();
        //$image->move(public_path('storage/images/users'), $filename);
        $data = [
            'nama_lengkap' => $request->nama_lengkap,
            'kelas_id' => $request->kelas_id,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'status' => "ON",
        //    'image' => $request->file('image')->getClientOriginalName(),
            'created_at' => now()
        ];
        // dd($data);
        $cekUsers = DB::table('users')->where('email', $request->email)->first();
        // dd($cekUsers);
        if ($cekUsers != null) {
            Alert::error('Email sudah terdaftar!');
            return redirect()->back()->withInput();
        }
        DB::table('users')->insert($data);
        Alert::success('Admin berhasil ditambah');
        return redirect('admin');
    }
    public function edit(Request $request)
    {
        $data['title'] = "Edit Admin";
        $data['role'] = ['1', '2', '3'];
        $data['status'] = ['ON', 'OFF'];
        $data['admin'] = DB::table('users')->where('id', $request->id)->first();
        $data['siswa'] = DB::table('users')->where('id', $request->id)->first();
        $data['kelas'] = DB::select("select * from kelas");
        $data['jurusan'] = DB::select("select * from jurusan");
        $data['phbnu'] = DB::select("select * from phbnu");
        $data['k_sertifikat'] = DB::select("select * from k_sertifikat");
        return view('backend.admin.edit', $data);
    }
    public function editProses(Request $request)
    {
        $getImage = DB::table('users')->where('id', $request->id)->first();

        $data = [
            'nis' => $request->nis,
            'nama_lengkap' => $request->nama_lengkap,
            'email' => $request->email,
            'kelas_id' => $request->kelas_id,
            'no_tlp' => $request->no_tlp,
            'tgl_lahir' => $request->tgl_lahir,
            'alamat' => $request->alamat,
            'role' => $request->role,
            'status' => $request->status,
            'updated_at' => now(),

            'akreditasi' => $request->akreditasi,
            'masaakreditasi' => $request->masaakreditasi,
            'statustanah' => $request->statustanah,
            'luastanah' => $request->luastanah,
            'sertifikat' => $request->sertifikat,
            'atasnama' => $request->atasnama,
            'phbnu' => $request->phbnu
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('image')) {
            if (!empty($getImage->image)) {
                $file_path = public_path() . '/storage/images/users/' . $getImage->image;
                File::delete($file_path);
            }

            $image = $request->file('image');
            $filename = $image->getClientOriginalName();
            $image->move(public_path('storage/images/users'), $filename);
            $data['image'] = $filename;
        }

        // dd($data);
        DB::table('users')->where('id', $request->id)->update($data);
        Alert::success('Admin berhasil diubah');
        return redirect('profile');
    }
    public function delete($id)
    {
        try {
            $getImage = DB::table('users')->where('id', $id)->first();
            $file_path = public_path() . '/storage/images/users/' . $getImage->image;
            File::delete($file_path);
            DB::table('users')->where('id', $id)->delete();
            // Alert::success('Category was successful deleted!');
            return redirect()->route('admin');
        } catch (Exception $e) {
            return response([
                'success' => false,
                'msg' => 'Error : ' . $e->getMessage() . ' Line : ' . $e->getLine() . ' File : ' . $e->getFile()
            ]);
        }
    }
    function changeStatus($request)
    {
        dd($request->all());
    }
}
