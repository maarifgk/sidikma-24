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
use App\Models\User;
use App\Models\Kelas;
use App\Notifications\ProposalMasuk;
use Illuminate\Support\Facades\Mail;
use App\Mail\ProposalSubmitted;


class ProposalController extends Controller
{
    public function view()
    {
        $data['title'] = "PENGAJUAN PROPOSAL";
        $data['proposal'] = DB::select("SELECT * FROM proposal ORDER BY id DESC");
        $data['perm_proposal'] = DB::table('proposal')
            ->where('kelas_id', request()->user()->kelas_id)
            ->orderBy('id', 'desc') // juga diurutkan kalau mau
            ->get();

        return view('backend.proposal.view', $data);
    }
    public function add()
    {
        $data['title'] = "Ajukan Permohonan Bantuan/Proposal";
        $data['proposal'] = DB::select("select * from proposal");
        $data['jenis_proposal'] = DB::select("select * from jenis_proposal");
        $data['kelas'] = DB::select("select * from kelas");

        return view('backend.proposal.add', $data);
    }
    public function addsarpras(Request $request)
    {
        // Hapus file lama jika ada
        $file_path = public_path('storage/dokumen/proposal/' . $request->proposal);
        File::delete($file_path);

        // Upload file baru
        $proposal = $request->file('proposal');
        $filename = $proposal->getClientOriginalName();
        $destinationPath = public_path('storage/dokumen/proposal/');
        $proposal->move($destinationPath, $filename);

        // Ambil data kelas
        $kelas = DB::table('kelas')->where('id', $request->kelas_id)->first();

        // Simpan data proposal
        $data = [
            'kelas_id' => $request->kelas_id,
            'jenis_proposal' => $request->jenis_proposal,
            'proposal' => $filename,
            'nominal' => $request->nominal,
            'nama_bank' => $request->nama_bank,
            'no_rekening' => $request->no_rekening,
            'an_rekening' => $request->an_rekening,
            'status' => "Dalam Peninjauan",
            'nominal_acc' => "-",
            'created_at' => now()
        ];

        DB::table('proposal')->insert($data);

        // Kirim email ke semua user dengan role 1 dan 4
        $users = \App\Models\User::whereIn('role', [1, 4])->get();

        foreach ($users as $user) {
            \Mail::to($user->email)->send(new \App\Mail\ProposalSubmitted([
                'nama_kelas' => $kelas->nama_kelas,
                'jenis_proposal' => $request->jenis_proposal,
                'nominal' => $request->nominal,
                'status' => 'Dalam Peninjauan'
            ]));
        }

        \RealRashid\SweetAlert\Facades\Alert::success('Permohonan Proposal Bantuan Berhasil Diajukan!');
        return redirect('proposal');
    }

    public function open(Request $request)
    {
        $data['title'] = "Data Permohonan Bantuan/Proposal";
        $data['proposal'] = DB::table('proposal')->where('id', $request->id)->first();
        return view('backend.proposal.open', $data);
    }

    public function viewFile($id)
    {
        $proposal = DB::table('proposal')->where('id', $id)->first();

        abort_unless($proposal, 404);

        $filePath = $this->resolveDocumentPath('proposal', $proposal->proposal ?? null);

        abort_unless($filePath, 404, 'File proposal tidak ditemukan.');

        return response()->file($filePath);
    }

    public function viewApprovedFile($id)
    {
        $proposal = DB::table('proposal')->where('id', $id)->first();

        abort_unless($proposal, 404);

        $filePath = $this->resolveDocumentPath('approve_proposal', $proposal->approve_proposal ?? null);

        abort_unless($filePath, 404, 'File approval proposal tidak ditemukan.');

        return response()->file($filePath);
    }

    public function openProses(Request $request)
    {
        $file_path = public_path() . '/storage/dokumen/approve_proposal/' . $request->approve_proposal;
        File::delete($file_path);
        $approve_proposal = $request->file('approve_proposal');
        $filename = $approve_proposal->getClientOriginalName();
        // Simpan ke public_html
        $destinationPath = $_SERVER['DOCUMENT_ROOT'] . '/storage/dokumen/approve_proposal/';
        $approve_proposal->move($destinationPath, $filename);
        //$approve_proposal->move(public_path('storage/dokumen/approve_proposal'), $filename);
        $data = [
            'approve_proposal' => $request->file('approve_proposal')->getClientOriginalName(),
            'nominal_acc' => $request->nominal_acc,
            'catatan' => $request->catatan,
            'status' => $request->status,
            'created_at' => now()
        ];
        // dd($data);
        DB::table('proposal')->where('id', $request->id)->update($data);
        Alert::success('Permohonan Bantuan/Proposal disetujui!');
        return redirect('proposal');
    }
    public function ubahStatus(Request $request)
    {
        try {
            \Log::info('Request diterima:', $request->all());

            $updated = DB::table('proposal')->where('id', $request->id)->update([
                'status' => $request->status,
                'keterangan_ditolak' => $request->keterangan_ditolak,
                'updated_at' => now()
            ]);

            if ($updated === 0) {
                return response()->json(['success' => false, 'message' => 'Data tidak ditemukan atau tidak berubah'], 404);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            \Log::error('Update gagal: ' . $e->getMessage(), [
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ], 500);
        }
    }
    public function delete($id)
    {
        try {
            // dd($id);
            DB::table('proposal')->where('id', $id)->delete();
            Alert::success('Data Berhasil Dihapus!');
            return redirect()->route('proposal');
        } catch (Exception $e) {
            return response([
                'success' => false,
                'msg'     => 'Error : ' . $e->getMessage() . ' Line : ' . $e->getLine() . ' File : ' . $e->getFile()
            ]);
        }
    }

    // Show edit form for data pengajuan proposal (role 1)
    public function editInfo()
    {
        if (request()->user()->role != 1) {
            abort(403);
        }
        $data['title'] = 'Edit Data Pengajuan Proposal';
        return view('backend.proposal.edit_info', $data);
    }

    // Update data pengajuan proposal
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
                'nb'      => strip_tags($request->input('nb')) ?? '',
                'link_proposal' => filter_var($request->input('link_proposal'), FILTER_SANITIZE_URL) ?? '',
            ];

            DB::table('aplikasi')->where('id', $id)->update([
                'info_proposal' => json_encode($payload),
            ]);

            $params['activity'] = 'Update Data Pengajuan Proposal';
            $params['detail'] = 'User ' . request()->user()->id . ' updated info_proposal';
            \App\Providers\Helper::log_transaction($params);

            Alert::success('Sukses', 'Data Pengajuan Proposal disimpan');
            return redirect('/proposal');
        } catch (Exception $e) {
            return response([
                'success' => false,
                'msg'     => 'Error : ' . $e->getMessage() . ' Line : ' . $e->getLine() . ' File : ' . $e->getFile()
            ]);
        }
    }

    private function resolveDocumentPath(string $folder, ?string $filename): ?string
    {
        if (!$filename) {
            return null;
        }

        $cleanFilename = basename($filename);
        $candidates = [
            public_path("storage/dokumen/{$folder}/{$cleanFilename}"),
            $_SERVER['DOCUMENT_ROOT'] . "/storage/dokumen/{$folder}/{$cleanFilename}",
        ];

        foreach ($candidates as $path) {
            if ($path && File::exists($path)) {
                return $path;
            }
        }

        return null;
    }
}
