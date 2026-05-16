<?php

namespace App\Http\Controllers;


use App\Providers\Helper;
use App\Support\MidtransPaymentSync;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;

class PembayaranController extends Controller
{
    protected function mobilePaymentBlockedMessage(): string
    {
        return 'Pembayaran mobile tidak dapat dilakukan karena sekolah atau kelas ini masih memiliki tagihan iuran yang belum lunas. Silakan selesaikan iuran terlebih dahulu.';
    }

    protected function hasOutstandingIuranForKelas(?int $kelasId): bool
    {
        if (!$kelasId) {
            return false;
        }

        return DB::table('tagihan')
            ->where('kelas_id', $kelasId)
            ->whereIn('jenis_pembayaran', [14, 16, 19])
            ->where('status', 'Belum Lunas')
            ->exists();
    }

    protected function validateMobileRoleTwoPayment(Request $request)
    {
        $user = $request->user();

        if (!$user || (int) $user->role !== 2) {
            return null;
        }

        $tagihan = DB::table('tagihan')
            ->where('id', $request->tagihan_id)
            ->where('user_id', $user->id)
            ->first();

        abort_if(!$tagihan, 404);

        if ((int) $tagihan->kelas_id !== (int) $user->kelas_id) {
            abort(403);
        }

        if ($this->hasOutstandingIuranForKelas((int) $user->kelas_id)) {
            return redirect()->route('mobile.role2.pembayaran')
                ->with('error', $this->mobilePaymentBlockedMessage());
        }

        return null;
    }


    public function view()
    {
        $data['title'] = "Pembayaran";
        $data['getSiswa'] = DB::select("select * from users where role in ('2','3') order by role desc, nama_lengkap asc");
        $data['thajaran'] = DB::select("select * from tahun_ajaran where active = 'ON'");
        $data['kelas'] = DB::select("select * from kelas");
        $data['siswa'] = "";
        $data['pembayaran_bulanan'] = "";
        $data['pembayaran_lainya'] = [];

        return view('backend.pembayaran.view', $data);
    }
    public function search(Request $request)
    {
        $data['title'] = "Pembayaran";
        $data['getSiswa'] = DB::select("select * from users where role in ('2','3') order by role desc, nama_lengkap asc");
        $data['thajaran'] = DB::select("select * from tahun_ajaran where active = 'ON'");
        $data['kelas'] = DB::select("select * from kelas");
        $data['siswa'] = DB::table('users')->join('tagihan', 'users.id', '=', 'tagihan.user_id')->join('kelas', 'kelas.id', '=', 'tagihan.kelas_id')->where('users.nis', $request->nis)->where('users.kelas_id', $request->kelas_id)->first();

        if (!empty($data['siswa']->user_id)) {
            MidtransPaymentSync::syncPendingPaymentsForUser((int) $data['siswa']->user_id);
        }

        $data['pembayaran_bulanan'] = DB::select("SELECT
        IF(COUNT(DISTINCT CASE WHEN p.status = 'Lunas' THEN p.bulan_id END) = 12, 'Lunas', 'Belum Lunas') as status_bayar,
        COALESCE(SUM(CASE WHEN p.status = 'Lunas' THEN p.nilai ELSE 0 END), 0) as total_bayar, t.thajaran_id, u.nis, ta.tahun, k.nama_kelas, jp.pembayaran, t.id
        FROM tagihan t  LEFT JOIN payment p on p.tagihan_id=t.id
        LEFT JOIN tahun_ajaran ta on ta.id=t.thajaran_id
        LEFT JOIN jenis_pembayaran jp on jp.id=t.jenis_pembayaran
        LEFT JOIN users u on u.id=t.user_id
        left join kelas k on k.id=t.kelas_id
        WHERE u.nis = '$request->nis'
        and t.jenis_pembayaran = '1'
        GROUP BY t.thajaran_id, u.nis, ta.tahun, jp.pembayaran, t.id");

        $latestPayments = DB::table('payment')
            ->selectRaw('MAX(id) as last_payment_id, tagihan_id')
            ->groupBy('tagihan_id');

        $data['pembayaran_lainya'] = DB::table('tagihan as t')
            ->select(
                't.*',
                'u.nama_lengkap',
                'k.nama_kelas',
                'ta.tahun',
                'jp.pembayaran',
                'u.nis',
                'p.order_id',
                'p.pdf_url',
                'p.metode_pembayaran',
                'p.status as status_payment'
            )
            ->leftJoin('users as u', 't.user_id', '=', 'u.id')
            ->leftJoin('tahun_ajaran as ta', 'ta.id', '=', 't.thajaran_id')
            ->leftJoin('jenis_pembayaran as jp', 'jp.id', '=', 't.jenis_pembayaran')
            ->leftJoinSub($latestPayments, 'lp', function ($join) {
                $join->on('lp.tagihan_id', '=', 't.id');
            })
            ->leftJoin('payment as p', 'p.id', '=', 'lp.last_payment_id')
            ->leftJoin('kelas as k', 'k.id', '=', 't.kelas_id')
            ->where('u.nis', $request->nis)
            ->where('t.jenis_pembayaran', '!=', '1')
            ->orderByDesc('t.id')
            ->get();


        $params['activity']    = "Search Pembayaran";
        $params['detail']    = "Search Pembayaran Nis '$request->nis' dan kelas Id '$request->kelas_id'";
        Helper::log_transaction($params);
        // dd($data['pembayaran_lainya']);
        if (!empty($data['pembayaran_bulanan']) || $data['pembayaran_lainya']->isNotEmpty()) {

            return view('backend.pembayaran.view', $data);
        } else {
            Alert::warning('Peringatan', 'SISWA BELUM ADA TAGIHAN');
            return view('backend.pembayaran.view', $data);
        }
    }



    public function spp($id_tagihan)
    {
        $data['title'] = "Riwayat Pembayaran Spp";
        // $data['id_tagihan'] = $id_tagihan;

        MidtransPaymentSync::syncPendingPaymentsForTagihan((int) $id_tagihan);

        $getDataUser[0] = DB::select("select user_id, thajaran_id, t.kelas_id, u.nis from tagihan t left join users u on t.user_id=u.id where t.id = '$id_tagihan'");
        $data['user_id'] = $getDataUser[0][0]->user_id;
        $data['thajaran_id'] = $getDataUser[0][0]->thajaran_id;
        $data['nis'] = $getDataUser[0][0]->nis;
        $data['kelas_id'] = $getDataUser[0][0]->kelas_id;
        $data['tagihan_id'] = $id_tagihan;
        $data['spp'] = DB::select("select s.*, u.nama_lengkap, ta.tahun, jp.pembayaran, b.nama_bulan from payment s
        left join users u on u.id=s.user_id left join bulan b on b.id=s.bulan_id left join tagihan t on t.id=s.tagihan_id
        left join tahun_ajaran ta on ta.id=t.thajaran_id left join jenis_pembayaran jp on jp.id=t.jenis_pembayaran
        where t.id = '$id_tagihan' order by bulan_id asc");
        $data['bulan'] = DB::select("SELECT id, nama_bulan FROM bulan WHERE id NOT IN (SELECT bulan_id FROM payment WHERE tagihan_id = '$id_tagihan')");
        $data['getNilai'] = DB::select("select nilai from tagihan where id = '$id_tagihan'")[0]->nilai;

        // dd($data['spp']);
        return view('backend.pembayaran.spp', $data);
    }
    public function sppAddProses(Request $request)
    {
        $dataMidtrans = json_decode($request->result_data);

        foreach ($request->bulan as $key => $bu) {

            $data[] = [
                'bulan_id' => $bu,
                'user_id' => $request->user_id,
                'tagihan_id' => $request->tagihan_id,
                'kelas_id' => $request->kelas_id,
                'nilai' => $request->getNilai,
                'order_id' => isset($dataMidtrans->order_id) == false ? null : $dataMidtrans->order_id,
                'pdf_url' => isset($dataMidtrans->pdf_url) == false ? null : $dataMidtrans->pdf_url,
                'metode_pembayaran' => $request->metode_pembayaran,
                'status' => $request->metode_pembayaran == "Online" ? "Pending" : 'Lunas',
                'created_at' => now(),
            ];
            // dd($key);
        }
        // dd($data);
        $params['activity']    = "Tambah Pembayaran Spp";
        $params['detail']    = "Tambah Pembayaran Spp dengan ID Tagihan '$request->tagihan_id' dan kelas Id '$request->kelas_id'";
        Helper::log_transaction($params);
        $getusers = DB::table('users')->where('id', $request->user_id)->first();
        DB::table('payment')->insert($data);
        MidtransPaymentSync::refreshTagihanStatuses([(int) $request->tagihan_id]);
        Helper::sendWhatsappMessage(
            $getusers->no_tlp ?? null,
            'Terima kasih, pembayaran Bulanan anda berhasil dengan nama siswa '
                . ($getusers->nama_lengkap ?? '-')
                . ' dengan nis '
                . ($getusers->nis ?? '-')
                . '. Silahkan cek tagihan anda di dashboard siswa'
        );
        $request->metode_pembayaran == "Manual" ? Alert::success('Success', 'Pembayaran Berhasil') : Alert::warning('Peringatan', 'Segera melakukan pembayaran!!!');
        return redirect("/pembayaran/spp/$request->tagihan_id");
    }
    public function payment($id_tagihan)
    {
        $data['title'] = "Payment";
        MidtransPaymentSync::syncPendingPaymentsForTagihan((int) $id_tagihan);
        $data['payment'] = DB::select("SELECT t.*, u.nama_lengkap, jp.pembayaran, ta.tahun, u.nis, u.email, u.no_tlp FROM tagihan t LEFT JOIN users u on u.id=t.user_id LEFT JOIN jenis_pembayaran jp on jp.id=t.jenis_pembayaran LEFT JOIN tahun_ajaran ta on ta.id=t.thajaran_id WHERE t.id = '$id_tagihan'");
        // dd($data['payment']);
        return view('backend.pembayaran.payment', $data);
    }

    public function paymentAddProses(Request $request)
    {
        // dd($request->all());
        if ($response = $this->validateMobileRoleTwoPayment($request)) {
            return $response;
        }

        $dataMidtrans = json_decode($request->result_data);
        // dd();
        $status = 'Lunas';
        $alertType = 'success';
        $alertMessage = 'Pembayaran Berhasil';

        if ($request->metode_pembayaran == "Online") {
            if ($request->result_type == 'success') {
                $status = 'Lunas';
                $alertType = 'success';
                $alertMessage = 'Pembayaran Berhasil';
            } elseif ($request->result_type == 'pending') {
                $status = 'Pending';
                $alertType = 'warning';
                $alertMessage = 'Segera melakukan pembayaran!!!';
            } else {
                $status = 'Failed';
                $alertType = 'error';
                $alertMessage = 'Pembayaran Gagal';
            }
        }

        $data = [
            'user_id' => $request->user_id,
            'tagihan_id' => $request->tagihan_id,
            'kelas_id' => $request->kelas_id,
            'nilai' => str_replace(',', '', str_replace('Rp. ', '', $request->nilai)),
            'order_id' => isset($dataMidtrans->order_id) == false ? null : $dataMidtrans->order_id,
            'pdf_url' => isset($dataMidtrans->pdf_url) == false ? null : $dataMidtrans->pdf_url,
            'metode_pembayaran' => $request->metode_pembayaran,
            'status' => $status,
            'created_at' => now(),
        ];
        // dd($data);
        $getusers = DB::table('users')->where('id', $request->user_id)->first();
        DB::table('payment')->insert($data);
        MidtransPaymentSync::refreshTagihanStatuses([(int) $request->tagihan_id]);
        Helper::sendWhatsappMessage(
            $getusers->no_tlp ?? null,
            'Terima kasih, pembayaran dengan jumlah '
                . $request->nilai
                . ' dengan nama siswa '
                . ($getusers->nama_lengkap ?? '-')
                . ' dengan nis '
                . ($getusers->nis ?? '-')
                . ' Berhasil. Silahkan cek tagihan anda di dashboard siswa'
        );

        if ($alertType == 'success') {
            Alert::success('Success', $alertMessage);
        } elseif ($alertType == 'warning') {
            Alert::warning('Peringatan', $alertMessage);
        } else {
            Alert::error('Error', $alertMessage);
        }

        if ($request->user() && (int) $request->user()->role === 2) {
            return redirect()->route('mobile.role2.pembayaran');
        }

        return redirect("/pembayaran/search?&kelas_id=$request->kelas_id&nis=$request->nis");
    }

    public function iuranAddProses(Request $request)
    {
        return $this->paymentAddProses($request);
    }

    function siswaByKelas($kelas_id)
    {

        // dd($kelas_id);

        // $query = DB::table('users')->where('kelas_id', $kelas_id)->where('role', 2)->where('status', '!=', 'Lulus')->get();
        if ($kelas_id != "Lulus") {
            $query = DB::select("select * from users where kelas_id = '$kelas_id' and role in ('2','3') and status != 'Lulus' order by role desc, nama_lengkap asc");
        } elseif ($kelas_id = "Lulus") {
            $query = DB::select("select * from users where status = 'Lulus' and role in ('2','3') order by role desc, nama_lengkap asc");
        }

        // dd($query);
        return response()->json($query);
    }
    public function deleteSpp($id)
    {
        try {
            // dd($id);
            $getUsers = DB::select("select * from payment p where p.id = '$id'");
            // dd($getUsers);
            DB::table('payment')->where('id', $id)->delete();
            Alert::success('Pembayaran berhasil dihapus');
            return redirect("/pembayaran/spp/" . $getUsers[0]->tagihan_id . "");
        } catch (Exception $e) {
            return response([
                'success' => false,
                'msg'     => 'Error : ' . $e->getMessage() . ' Line : ' . $e->getLine() . ' File : ' . $e->getFile()
            ]);
        }
    }

    /**
     * Show edit form for Informasi Pembayaran (only for role 1)
     */
    public function editInfo()
    {
        if (request()->user()->role != 1) {
            abort(403);
        }
        $data['title'] = 'Edit Informasi Pembayaran';
        return view('backend.pembayaran.edit_info', $data);
    }

    /**
     * Update the Informasi Pembayaran content stored in aplikasi table
     */
    public function updateInfo(Request $request)
    {
        if (request()->user()->role != 1) {
            abort(403);
        }

        try {
            $id = $request->id ?? 1;
            // sanitize numeric inputs: remove non-digits and include editable labels
            $payload = [
                'label_iuran_ibtidaiyah' => strip_tags($request->input('label_iuran_ibtidaiyah')) ?? '',
                'iuran_ibtidaiyah' => preg_replace('/\D/', '', $request->input('iuran_ibtidaiyah')) ?? '0',

                'label_iuran_tsanawiyah' => strip_tags($request->input('label_iuran_tsanawiyah')) ?? '',
                'iuran_tsanawiyah' => preg_replace('/\D/', '', $request->input('iuran_tsanawiyah')) ?? '0',

                'label_iuran_guru_asn_sertifikasi' => strip_tags($request->input('label_iuran_guru_asn_sertifikasi')) ?? '',
                'iuran_guru_asn_sertifikasi' => preg_replace('/\D/', '', $request->input('iuran_guru_asn_sertifikasi')) ?? '0',

                'label_iuran_guru_asn_belum' => strip_tags($request->input('label_iuran_guru_asn_belum')) ?? '',
                'iuran_guru_asn_belum' => preg_replace('/\D/', '', $request->input('iuran_guru_asn_belum')) ?? '0',

                'label_iuran_guru_yayasan_sertifikasi' => strip_tags($request->input('label_iuran_guru_yayasan_sertifikasi')) ?? '',
                'iuran_guru_yayasan_sertifikasi' => preg_replace('/\D/', '', $request->input('iuran_guru_yayasan_sertifikasi')) ?? '0',

                'label_iuran_guru_yayasan_belum' => strip_tags($request->input('label_iuran_guru_yayasan_belum')) ?? '',
                'iuran_guru_yayasan_belum' => preg_replace('/\D/', '', $request->input('iuran_guru_yayasan_belum')) ?? '0',

                'label_sk_penerbitan' => strip_tags($request->input('label_sk_penerbitan')) ?? '',
                'sk_penerbitan' => preg_replace('/\D/', '', $request->input('sk_penerbitan')) ?? '0',

                'label_sk_perpanjangan' => strip_tags($request->input('label_sk_perpanjangan')) ?? '',
                'sk_perpanjangan' => preg_replace('/\D/', '', $request->input('sk_perpanjangan')) ?? '0',
            ];

            DB::table('aplikasi')->where('id', $id)->update([
                'info_pembayaran' => json_encode($payload),
            ]);

            $params['activity'] = "Update Informasi Pembayaran";
            $params['detail'] = "User " . request()->user()->id . " updated pembayaran info";
            Helper::log_transaction($params);

            Alert::success('Sukses', 'Informasi Pembayaran disimpan');
            return redirect('/pembayaran');
        } catch (Exception $e) {
            return response([
                'success' => false,
                'msg'     => 'Error : ' . $e->getMessage() . ' Line : ' . $e->getLine() . ' File : ' . $e->getFile()
            ]);
        }
    }
}
