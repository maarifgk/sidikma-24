<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class PDFController extends Controller
{
    public function showUploadForm()
    {
        return view('backend.tools.pdf_upload');
    }

    public function upload(Request $request)
    {
        $request->validate([
            'pdf_file' => 'required|file|mimes:pdf|max:1048576', // maksimal 1 GB
        ]);

        $file = $request->file('pdf_file');
        $filename = time().'_'.$file->getClientOriginalName();

        $destinationPath = $_SERVER['DOCUMENT_ROOT'] . '/storage/dokumen/proses_tandatangan/';

        if (!File::exists($destinationPath)) {
            File::makeDirectory($destinationPath, 0755, true);
        }

        $file->move($destinationPath, $filename);

        return redirect()->route('tools.pdf.upload.form', ['file' => $filename])
            ->with('success', 'File berhasil diupload!');
    }
}
