<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ModulController extends Controller
{
    public function index()
    {
        $modul = DB::table('modul')->get();
        return view('backend.modul.index', compact('modul'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kelas' => 'required',
            'jenis_modul' => 'required',
            'semester' => 'required',
            'bab' => 'required',
            'mapel' => 'required',
            'file' => 'required|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx|max:1048576'
        ]);

        // 📂 simpan ke public_html/modul/
        $documentRoot = $_SERVER['DOCUMENT_ROOT']; 
        $uploadPath = $documentRoot . '/modul';

        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        $fileName = time() . '_' . $request->file->getClientOriginalName();
        $request->file->move($uploadPath, $fileName);

        DB::table('modul')->insert([
            'kelas' => $request->kelas,
            'jenis_modul' => $request->jenis_modul,
            'semester' => $request->semester,
            'bab' => $request->bab,
            'mapel' => $request->mapel,
            'file_path' => 'modul/' . $fileName,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('modul.index')->with('success', 'Modul berhasil diupload.');
    }

    public function destroy($id)
    {
        $modul = DB::table('modul')->where('id', $id)->first();

        if ($modul) {
            $filePath = $_SERVER['DOCUMENT_ROOT'] . '/' . $modul->file_path;
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            DB::table('modul')->where('id', $id)->delete();
        }

        return redirect()->route('modul.index')->with('success', 'Modul berhasil dihapus.');
    }
}
