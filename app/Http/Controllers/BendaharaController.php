<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bendahara;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class BendaharaController extends Controller
{
    public function index()
    {
        // Ambil data laporan + join tabel jenis
        $laporan = DB::table('bendaharas')
            ->leftJoin('jenis_pemasukan', 'bendaharas.jenis_pemasukan_id', '=', 'jenis_pemasukan.id')
            ->leftJoin('jenis_pengeluaran', 'bendaharas.jenis_pengeluaran_id', '=', 'jenis_pengeluaran.id')
            ->select(
                'bendaharas.*',
                'jenis_pemasukan.jenis as nama_pemasukan',
                'jenis_pengeluaran.jenis as nama_pengeluaran'
            )
            ->orderBy('bendaharas.created_at', 'desc')
            ->get();

        $totalPemasukan = DB::table('bendaharas')->sum('pemasukan');
        $totalPengeluaran = DB::table('bendaharas')->sum('pengeluaran');
        $saldo = $totalPemasukan - $totalPengeluaran;

        // Rekap per jenis pemasukan
        $rekapPemasukan = DB::table('bendaharas')
            ->join('jenis_pemasukan', 'bendaharas.jenis_pemasukan_id', '=', 'jenis_pemasukan.id')
            ->select('jenis_pemasukan.jenis', DB::raw('SUM(bendaharas.pemasukan) as total'))
            ->groupBy('jenis_pemasukan.jenis')
            ->get();

        // Rekap per jenis pengeluaran
        $rekapPengeluaran = DB::table('bendaharas')
            ->join('jenis_pengeluaran', 'bendaharas.jenis_pengeluaran_id', '=', 'jenis_pengeluaran.id')
            ->select('jenis_pengeluaran.jenis', DB::raw('SUM(bendaharas.pengeluaran) as total'))
            ->groupBy('jenis_pengeluaran.jenis')
            ->get();

        return view('backend.bendahara.view', compact(
            'laporan', 'totalPemasukan', 'totalPengeluaran', 'saldo',
            'rekapPemasukan', 'rekapPengeluaran'
        ));
    }

    public function destroy($id)
    {
        Bendahara::destroy($id);
        return redirect()->route('bendahara.index')->with('success', 'Data berhasil dihapus.');
    }

    public function create(Request $request)
    {
        $type = $request->get('type', 'pemasukan'); // default pemasukan

        $jenisPemasukan = [];
        $jenisPengeluaran = [];

        if ($type == 'pemasukan') {
            $jenisPemasukan = DB::table('jenis_pemasukan')->orderBy('jenis')->get();
        } elseif ($type == 'pengeluaran') {
            $jenisPengeluaran = DB::table('jenis_pengeluaran')->orderBy('jenis')->get();
        }

        return view('backend.bendahara.create', compact('type', 'jenisPemasukan', 'jenisPengeluaran'));
    }

    public function store(Request $request)
    {
        $type = $request->type;

        $rules = [
            'tanggal' => 'required|date',
            'uraian' => 'required|string',
            'pemasukan' => 'nullable|numeric',
            'pengeluaran' => 'nullable|numeric',
            'bukti_transaksi' => 'nullable|file|mimes:jpeg,jpg,png,pdf|max:1048576'
        ];

        if ($type == 'pemasukan') {
            $rules['jenis_pemasukan_id'] = 'required|exists:jenis_pemasukan,id';
        }

        $request->validate($rules);

        $data = [
            'tanggal' => $request->tanggal,
            'uraian' => $request->uraian,
            'pemasukan' => $type == 'pemasukan' ? $request->pemasukan : 0,
            'pengeluaran' => $type == 'pengeluaran' ? $request->pengeluaran : 0,
            'jenis_pemasukan_id' => $type == 'pemasukan' ? $request->jenis_pemasukan_id : null,
            'jenis_pengeluaran_id' => $type == 'pengeluaran' ? $request->jenis_pengeluaran_id : null,
            'created_at' => now(),
        ];

        if ($request->hasFile('bukti_transaksi')) {
            $file = $request->file('bukti_transaksi');
            $filename = time() . '_' . $file->getClientOriginalName();
            $destinationPath = $_SERVER['DOCUMENT_ROOT'] . '/storage/dokumen/bukti_transaksi/';
            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true);
            }
            $file->move($destinationPath, $filename);
            $data['bukti_transaksi'] = 'dokumen/bukti_transaksi/' . $filename;
        }

        // simpan langsung ke DB tanpa model
        DB::table('bendaharas')->insert($data);

        return redirect()->route('bendahara.index')->with('success', 'Data berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $item = DB::table('bendaharas')
            ->leftJoin('jenis_pemasukan', 'bendaharas.jenis_pemasukan_id', '=', 'jenis_pemasukan.id')
            ->leftJoin('jenis_pengeluaran', 'bendaharas.jenis_pengeluaran_id', '=', 'jenis_pengeluaran.id')
            ->select(
                'bendaharas.*',
                'jenis_pemasukan.jenis as nama_pemasukan',
                'jenis_pengeluaran.jenis as nama_pengeluaran'
            )
            ->where('bendaharas.id', $id)
            ->first();

        $jenisPemasukan = DB::table('jenis_pemasukan')->get();
        $jenisPengeluaran = DB::table('jenis_pengeluaran')->get();

        return view('backend.bendahara.edit', compact('item', 'jenisPemasukan', 'jenisPengeluaran'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'uraian' => 'required|string',
            'pemasukan' => 'nullable|numeric',
            'pengeluaran' => 'nullable|numeric',
            'bukti_transaksi' => 'nullable|file|mimes:jpeg,jpg,png,pdf|max:1048576'
        ]);

        $item = Bendahara::findOrFail($id);

        $data = [
            'tanggal' => $request->tanggal,
            'uraian' => $request->uraian,
            'pemasukan' => $request->pemasukan ?: null,
            'pengeluaran' => $request->pengeluaran ?: null,
            'updated_at' => now()
        ];

        if ($request->hasFile('bukti_transaksi')) {
            // Hapus file lama jika ada
            if ($item->bukti_transaksi) {
                $oldFilePath = public_path('/storage/' . $item->bukti_transaksi);
                if (File::exists($oldFilePath)) {
                    File::delete($oldFilePath);
                }
            }

            $file = $request->file('bukti_transaksi');
            $filename = time() . '_' . $file->getClientOriginalName();
            $destinationPath = $_SERVER['DOCUMENT_ROOT'] . '/storage/dokumen/bukti_transaksi/';
            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true);
            }
            $file->move($destinationPath, $filename);
            $data['bukti_transaksi'] = 'dokumen/bukti_transaksi/' . $filename;
        }

        $item->update($data);

        return redirect()->route('bendahara.index')->with('success', 'Data berhasil diperbarui.');
    }
}
