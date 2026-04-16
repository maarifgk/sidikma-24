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
use Carbon\Carbon;

class UsulanController extends Controller
{
    public function view()
    {
        $data['title'] = "USULAN SK GURU DAN PERGAWAI BARU";
        $data['usulan'] = DB::select("select * from usulan");
        $data['ketugasan'] = DB::select("select * from ketugasan");
        $data['jurusan'] = DB::select("select * from jurusan");
        $data['pengajuan'] = DB::table('usulan')->where('kelas', request()->user()->kelas_id)->get();
        return view('backend.usulan.view', $data);
    }
    public function open($id)
    {
        $data['title'] = "Data Usulan SK Baru";
        $data['usulan'] = DB::table('usulan')->where('id', $id)->first();
        Carbon::setLocale('id');
        return view('backend.usulan.open', $data);
    }
    public function add()
    {
        $data['title'] = "Input Data Usulan Guru & Pegawai Baru";
        $data['kelas'] = DB::select("select * from kelas");
        $data['ketugasan'] = DB::select("select * from ketugasan");
        $data['jurusan'] = DB::select("select * from jurusan");
        return view('backend.usulan.add', $data);
    }
    public function addsarpras(Request $request)
    {
        $file_path = public_path() . '/storage/images/users/' . $request->foto;
        File::delete($file_path);
        $foto = $request->file('foto');
        $filename = $foto->getClientOriginalName();
        // Simpan ke public_html
        $destinationPath = $_SERVER['DOCUMENT_ROOT'] . '/storage/images/users/';
        $foto->move($destinationPath, $filename);
        //$foto->move(public_path('storage/images/users'), $filename);
        
        $file_path = public_path() . '/storage/dokumen/ijazah/' . $request->ijazah;
        File::delete($file_path);
        $ijazah = $request->file('ijazah');
        $filename = $ijazah->getClientOriginalName();
        // Simpan ke public_html
        $destinationPath = $_SERVER['DOCUMENT_ROOT'] . '/storage/dokumen/ijazah/';
        $ijazah->move($destinationPath, $filename);
        //$ijazah->move(public_path('storage/dokumen/ijazah'), $filename);
        
        $file_path = public_path() . '/storage/dokumen/permohonan/' . $request->permohonan;
        File::delete($file_path);
        $permohonan = $request->file('permohonan');
        $filename = $permohonan->getClientOriginalName();
        // Simpan ke public_html
        $destinationPath = $_SERVER['DOCUMENT_ROOT'] . '/storage/dokumen/permohonan/';
        $permohonan->move($destinationPath, $filename);
        //$permohonan->move(public_path('storage/dokumen/permohonan'), $filename);
        
        $file_path = public_path() . '/storage/dokumen/pernyataan/' . $request->pernyataan;
        File::delete($file_path);
        $pernyataan = $request->file('pernyataan');
        $filename = $pernyataan->getClientOriginalName();
        // Simpan ke public_html
        $destinationPath = $_SERVER['DOCUMENT_ROOT'] . '/storage/dokumen/pernyataan/';
        $pernyataan->move($destinationPath, $filename);
        //$pernyataan->move(public_path('storage/dokumen/pernyataan'), $filename);
        $data = [
            'id' => $request->id,
            'ewanugk' => $request->ewanugk,
            'nama' => $request->nama,
            'email' => $request->email,
            'no_telepon' => $request->no_telepon,
            'kelas' => $request->kelas,
            'status_kepegawaian' => $request->status_kepegawaian,
            'tempat_lahir' => $request->tempat_lahir,
            'tanggal_lahir' => $request->tanggal_lahir,
            'tmt_pertama' => $request->tmt_pertama,
            'ketugasan' => $request->ketugasan,
            'no_mitra' => $request->no_mitra,
            'nuptk' => $request->nuptk,
            'ptt_lulus' => $request->ptt_lulus,
            'p_studi' => $request->p_studi,
            'foto' => $request->file('foto')->getClientOriginalName(),
            'ijazah' => $request->file('ijazah')->getClientOriginalName(),
            'permohonan' => $request->file('permohonan')->getClientOriginalName(),
            'pernyataan' => $request->file('pernyataan')->getClientOriginalName(),
            's_pengajuan' => "Dalam Peninjauan",
            'created_at' => now()
        ];
        // dd($data);
        $cekUsers = DB::table('usulan')->where('email', $request->email)->first();
        // dd($cekUsers);
        if ($cekUsers != null) {
            Alert::error('Email sudah terdaftar!');
            return redirect()->back()->withInput();
        }
        // Kirim email ke semua user dengan role 1 dan 4
        $users = \App\Models\User::whereIn('role', [1, 4])->get();

        foreach ($users as $user) {
            \Mail::to($user->email)->send(new \App\Mail\SkBaruSubmitted([
            'id' => $request->id,
            'ewanugk' => $request->ewanugk,
            'nama' => $request->nama,
            'email' => $request->email,
            'no_telepon' => $request->no_telepon,
            'kelas' => $request->kelas,
            'status_kepegawaian' => $request->status_kepegawaian,
            'tempat_lahir' => $request->tempat_lahir,
            'tanggal_lahir' => $request->tanggal_lahir,
            'tmt_pertama' => $request->tmt_pertama,
            'ketugasan' => $request->ketugasan,
            'no_mitra' => $request->no_mitra,
            'nuptk' => $request->nuptk,
            'ptt_lulus' => $request->ptt_lulus,
            'p_studi' => $request->p_studi,
            'foto' => $request->file('foto')->getClientOriginalName(),
            'ijazah' => $request->file('ijazah')->getClientOriginalName(),
            'permohonan' => $request->file('permohonan')->getClientOriginalName(),
            'pernyataan' => $request->file('pernyataan')->getClientOriginalName(),
            'created_at' => now()
            ]));
        }
        // dd($data);
        DB::table('usulan')->insert($data);
        Alert::success('Data Guru & Pegawai Baru Berhasil Dikirim!');
        return redirect('usulan');
    }
    public function edit($id)
    {
        $data['title'] = "Input Data Usulan Guru & Pegawai Baru";
        $data['kelas'] = DB::select("select * from kelas");
        $data['ketugasan'] = DB::select("select * from ketugasan");
        $data['jurusan'] = DB::select("select * from jurusan");
        $data['usulan'] = DB::table('usulan')->where('id', $id)->first();
        return view('backend.usulan.edit', $data);
    }
    public function editProses(Request $request)
    {
        $data = [
            'ewanugk' => $request->ewanugk,
            'nama' => $request->nama,
            'email' => $request->email,
            'no_telepon' => $request->no_telepon,
            'kelas' => $request->kelas,
            'status_kepegawaian' => $request->status_kepegawaian,
            'tempat_lahir' => $request->tempat_lahir,
            'tanggal_lahir' => $request->tanggal_lahir,
            'tmt_pertama' => $request->tmt_pertama,
            'ketugasan' => $request->ketugasan,
            'nuptk' => $request->nuptk,
            'ptt_lulus' => $request->ptt_lulus,
            'p_studi' => $request->p_studi,
            's_pengajuan' => $request->s_pengajuan,

        ];
        // dd($request->id);
        DB::table('usulan')->where('id', $request->id)->update($data);
        Alert::success('Data berhasil diubah');
        return redirect('usulan');
    }
    public function delete($id)
    {
        try {
            // dd($id);
            DB::table('usulan')->where('id', $id)->delete();
            Alert::success('Data Berhasil Dihapus!');
            return redirect()->route('usulan');
        } catch (Exception $e) {
            return response([
                'success' => false,
                'msg'     => 'Error : ' . $e->getMessage() . ' Line : ' . $e->getLine() . ' File : ' . $e->getFile()
            ]);
        }
    }

    // Show edit form for kelengkapan usulan (role 1)
    public function editInfo()
    {
        if (request()->user()->role != 1) {
            abort(403);
        }
        $data['title'] = 'Edit Kelengkapan Usulan Guru Baru';
        return view('backend.usulan.edit_info', $data);
    }

    // Update kelengkapan usulan
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
                'label_4_a' => strip_tags($request->input('label_4_a')) ?? '',
                'label_4_b' => strip_tags($request->input('label_4_b')) ?? '',
                'label_4_c' => strip_tags($request->input('label_4_c')) ?? '',
                'label_4_d' => strip_tags($request->input('label_4_d')) ?? '',
                'label_4_e' => strip_tags($request->input('label_4_e')) ?? '',
            ];

            DB::table('aplikasi')->where('id', $id)->update([
                'info_usulan' => json_encode($payload),
            ]);

            $params['activity'] = 'Update Kelengkapan Usulan';
            $params['detail'] = 'User ' . request()->user()->id . ' updated info_usulan';
            \App\Providers\Helper::log_transaction($params);

            Alert::success('Sukses', 'Kelengkapan Usulan disimpan');
            return redirect('/usulan');
        } catch (Exception $e) {
            return response([
                'success' => false,
                'msg'     => 'Error : ' . $e->getMessage() . ' Line : ' . $e->getLine() . ' File : ' . $e->getFile()
            ]);
        }
    }
}
