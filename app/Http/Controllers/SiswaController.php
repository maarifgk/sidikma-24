<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;



class SiswaController extends Controller
{
    public function index()
    {
        $data['title'] = "Guru/Pegawai";
        $data['periode'] = DB::select("select * from periode");
        $data['siswa'] = DB::select("select u.*, k.nama_kelas, j.nama_jurusan from users u left join kelas k on u.kelas_id=k.id left join jurusan j on u.jurusan_id=j.id where role = '2' and u.status != 'Lulus'");
        return view('backend.siswa.index', $data);
    }
    public function open($id)
    {
        $data['title'] = "Data Guru/Pegawai";
        $data['periode'] = DB::select("select * from periode");
        $data['siswa'] = DB::table('users')->where('id', $id)->first();
        $data['profile'] = DB::table('users')->select('users.*', 'kelas.nama_kelas', 'jurusan.nama_jurusan','ketugasan.ketugasan')->leftJoin('kelas', 'kelas.id', '=', 'users.kelas_id')->leftJoin('jurusan', 'jurusan.id', '=', 'users.jurusan_id')->leftJoin('ketugasan', 'ketugasan.id', '=', 'users.ketugasan')->where('users.id', request()->user()->id)->first();
        Carbon::setLocale('id');
        return view('backend.siswa.open', $data);
    }
    public function add()
    {
        $data['title'] = "Tambah Guru/Pegawai";
        $data['kelas'] = DB::select("select * from kelas");
        $data['jurusan'] = DB::select("select * from jurusan");
        $data['ketugasan'] = DB::select("select * from ketugasan");
        $data['periode'] = DB::select("select * from periode");
        return view('backend.siswa.add', $data);
    }
    public function addSiswa(Request $request)
    {
        $isPnsJurusan = in_array((int) $request->jurusan_id, [5, 8], true);
        $file_path = public_path() . '/storage/images/users/' . $request->image;
        File::delete($file_path);
        $image = $request->file('image');
        $filename = $image->getClientOriginalName();
        // Simpan ke public_html
        $destinationPath = $_SERVER['DOCUMENT_ROOT'] . '/storage/images/users/';
        $image->move($destinationPath, $filename);
        //$image->move(public_path('storage/images/users'), $filename);
        
        //$file_path = public_path() . '/storage/dokumen/sk/' . $request->sk;
        //File::delete($file_path);
        //$sk = $request->file('sk');
        //$filename = $sk->getClientOriginalName();
        //$sk->move(public_path('storage/dokumen/sk'), $filename);
        $data = [
            'nis' => $request->nis,
            'nama_lengkap' => $request->nama_lengkap,
            'email' => $request->email,
            'no_tlp' => $request-> Kosong,
            'nuptk' => $request->nuptk,
            'nip' => $isPnsJurusan ? $request->nip : null,
            'pangkat_golongan' => $isPnsJurusan ? $request->pangkat_golongan : null,
            'ptt_lulus' => $request->ptt_lulus,
            'p_studi' => $request->p_studi,
            'kelas_id' => $request->kelas_id,
            'jurusan_id' => $request->jurusan_id,
            'tempat_lahir' => $request->tempat_lahir,
            'tgl_lahir' => $request->tgl_lahir,
            'tmt' => $request->tmt,
            'ketugasan' => $request->ketugasan,
            'no_ortu' => $request->no_ortu,
            'password' => Hash::make($request->password),
            'alamat' => $request->alamat,
            'status' => "ON",
            'image' => $request->file('image')->getClientOriginalName(),
            //'sk' => $request->file('sk')->getClientOriginalName(),
            'periode' => $request->periode,
            'role' => 2,
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
        Alert::success('Guru/Pegawai berhasil ditambah');
        return redirect('siswa');
    }
    public function edit($id)
    {
        $data['title'] = "Menu Edit";
        $data['status'] = ['ON', 'OFF'];
        $data['siswa'] = DB::table('users')->where('id', $id)->first();
        $data['kelas'] = DB::select("select * from kelas");
        $data['jurusan'] = DB::select("select * from jurusan");
        $data['ketugasan'] = DB::select("select * from ketugasan");
        $data['periode'] = DB::select("select * from periode");
        
        $data['phbnu'] = DB::select("select * from phbnu");
        $data['k_sertifikat'] = DB::select("select * from k_sertifikat");
        $data['role'] = ['2', '3'];
        return view('backend.siswa.edit', $data);
    }
    public function editProses(Request $request)
    {
        // Ambil data lama user
        $user = DB::table('users')->where('id', $request->id)->first();
        $isPnsJurusan = in_array((int) $request->jurusan_id, [5, 8], true);
    
        $data = [
            'nis' => $request->nis,
            'nama_lengkap' => $request->nama_lengkap,
            'email' => $request->email,
            'no_tlp' => $request->no_tlp,
            'nuptk' => $request->nuptk,
            'nip' => $isPnsJurusan ? $request->nip : null,
            'pangkat_golongan' => $isPnsJurusan ? $request->pangkat_golongan : null,
            'ptt_lulus' => $request->ptt_lulus,
            'p_studi' => $request->p_studi,
            'kelas_id' => $request->kelas_id,
            'jurusan_id' => $request->jurusan_id,
            'tempat_lahir' => $request->tempat_lahir,
            'tgl_lahir' => $request->tgl_lahir,
            'tmt' => $request->tmt,
            'ketugasan' => $request->ketugasan,
            'no_ortu' => $request->no_ortu,
            'alamat' => $request->alamat,
            'periode' => $request->periode,
            'status' => $request->status,
            'role' => $request->role,
            'updated_at' => now(),
    
            'thn_pelajaran' => $request->thn_pelajaran,
            'kelas1' => $request->kelas1,
            'kelas2' => $request->kelas2,
            'kelas3' => $request->kelas3,
            'kelas4' => $request->kelas4,
            'kelas5' => $request->kelas5,
            'kelas6' => $request->kelas6,
            'kelas7' => $request->kelas7,
            'kelas8' => $request->kelas8,
            'kelas9' => $request->kelas9,
            'jumlahsiswa' => $request->jumlahsiswa,
    
            'akreditasi' => $request->akreditasi,
            'masaakreditasi' => $request->masaakreditasi,
            'statustanah' => $request->statustanah,
            'luastanah' => $request->luastanah,
            'sertifikat' => $request->sertifikat,
            'atasnama' => $request->atasnama,
            'phbnu' => $request->phbnu
        ];
    
        // ✅ Update password jika diisi
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }
    
        // ✅ Upload dan hapus file lama jika ada file baru untuk 'image'
        if ($request->hasFile('image')) {
            // Hapus file lama jika ada
            File::delete(public_path('storage/images/users/' . $user->image));
            $image = $request->file('image');
            $filename = $image->getClientOriginalName();
    
            // Tentukan lokasi penyimpanan di public_html
            $destinationPath = $_SERVER['DOCUMENT_ROOT'] . '/storage/images/users/';
            $image->move($destinationPath, $filename);
    
            // Simpan nama file ke database
            $data['image'] = $filename;
        }
    
        // ✅ Upload dan hapus file lama jika ada file baru untuk 'sk01_2025'
        if ($request->hasFile('sk01_2025')) {
            // Hapus file lama jika ada
            File::delete(public_path('storage/dokumen/sk01_2025/' . $user->sk01_2025));
            $sk01_2025 = $request->file('sk01_2025');
            $filename = $sk01_2025->getClientOriginalName();
    
            // Tentukan lokasi penyimpanan di public_html
            $destinationPath = $_SERVER['DOCUMENT_ROOT'] . '/storage/dokumen/sk01_2025/';
            $sk01_2025->move($destinationPath, $filename);
    
            // Simpan nama file ke database
            $data['sk01_2025'] = $filename;
        }
    
        // ✅ Upload dan hapus file lama jika ada file baru untuk 'skbfr2025'
        if ($request->hasFile('skbfr2025')) {
            // Hapus file lama jika ada
            File::delete(public_path('storage/dokumen/skbfr2025/' . $user->skbfr2025));
            $skbfr2025 = $request->file('skbfr2025');
            $filename = $skbfr2025->getClientOriginalName();
    
            // Tentukan lokasi penyimpanan di public_html
            $destinationPath = $_SERVER['DOCUMENT_ROOT'] . '/storage/dokumen/skbfr2025/';
            $skbfr2025->move($destinationPath, $filename);
    
            // Simpan nama file ke database
            $data['skbfr2025'] = $filename;
        }
    
        // ✅ Update data ke database
        DB::table('users')->where('id', $request->id)->update($data);
    
        Alert::success('Data berhasil diubah');
        if (Auth::user()->role == 1) {
            return redirect('profile_sekolah');
        } elseif (Auth::user()->role == 4) {
            return redirect('siswa');
        } else {
            return redirect('/'); // default fallback (optional)
        }

    }    
    public function delete($id)
    {
        try {
            // dd($id);
            $getImage = DB::table('users')->where('id', $id)->first();
            $file_path = public_path() . '/storage/images/users/' . $getImage->image;
            File::delete($file_path);

            DB::table('users')->where('id', $id)->delete();
            Alert::success('Guru/Pegawai berhasil dihapus');
            return redirect()->route('siswa');
        } catch (Exception $e) {
            return response([
                'success' => false,
                'msg'     => 'Error : ' . $e->getMessage() . ' Line : ' . $e->getLine() . ' File : ' . $e->getFile()
            ]);
        }
    }
     public function download($filename)
    {
        // Pastikan file berada di direktori yang tepat, misalnya di storage/app/public
        $getSk = DB::table('users')->where('id', $request->id)->first();
            $file_path = public_path() . '/storage/dokumen/sk/' . $getSk->sk;
            File::delete($file_path);
            $sk = $request->file('sk');
            // dd($getImage->image);
            $filename = $sk->getClientOriginalName();
            $sk->move(public_path('storage/dokumen/sk'), $filename);

        if (file_exists($path)) {
            return response()->download($path);
        } else {
            return abort(404); // Jika file tidak ditemukan
        }
    }
    public function alumni()
    {
        $data['title'] = "Kesiswaan";
        $data['alumni'] = DB::select("select u.*, k.nama_kelas, j.nama_jurusan from users u left join kelas k on u.kelas_id=k.id left join jurusan j on u.jurusan_id=j.id where role = '2' and u.status = 'Lulus'");
        // $data['tunggakan'] = DB::table('tagihan')->where('status', 'Belum Lunas')->first();
        return view('backend.siswa.alumni', $data);
    }
    public function tunggakan($user_id)
    {
        $data['title'] = "Tunggakan Guru/Pegawai";
        $data['tunggakan'] = DB::select("SELECT z.*, z.tagihan - z.bayar AS tunggakan FROM (
  SELECT t.user_id, u.nama_lengkap, ta.tahun, jp.pembayaran,
  CASE
    WHEN t.jenis_pembayaran = 1 THEN t.nilai * 12
    ELSE t.nilai
    END AS tagihan, SUM(IFNULL(p.nilai, 0)) AS bayar
  FROM tagihan t
  LEFT JOIN payment p on p.tagihan_id=t.id
  LEFT JOIN tahun_ajaran ta on ta.id=t.thajaran_id
  LEFT JOIN jenis_pembayaran jp on jp.id=t.jenis_pembayaran
  INNER JOIN users u on u.id = t.user_id
  GROUP BY t.user_id, jp.pembayaran, ta.tahun, t.jenis_pembayaran, t.nilai
) z
WHERE z.user_id = '$user_id' ORDER BY tunggakan DESC");
        return view('backend.siswa.tunggakan', $data);
    }
}
