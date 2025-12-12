<?php

namespace App\Http\Controllers;


use App\Providers\Helper;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use RealRashid\SweetAlert\Facades\Alert;

class PembayaranController extends Controller
{


    public function view()
    {
        $data['title'] = "Pembayaran";
        $data['getSiswa'] = DB::select("select * from users where role = '2'");
        $data['thajaran'] = DB::select("select * from tahun_ajaran where active = 'ON'");
        $data['kelas'] = DB::select("select * from kelas");
        $data['siswa'] = "";
        $data['pembayaran_bulanan'] = "";
        $data['pembayaran_lainya'] = [];

        return view('backend.pembayaran.view', $data);
    }
    public function search(Request $request)
    {
        $getOrderId = DB::select("select p.*, u.nis, u.nama_lengkap, u.no_tlp from users u left join payment p on p.user_id=u.id where u.nis = '$request->nis' and p.status = 'Pending' ORDER BY p.created_at DESC");
        // dd($getOrderId);
        if (isset($getOrderId)) {
            foreach ($getOrderId as $ord) {
                // dead($getorderid[$i]['order_id']);

                $headers = array(
                    'Content-Type:application/json',
                    'Authorization: Basic ' . base64_encode(Helper::apk()->serverKey)
                );

                // the url of the API you are contacting to 'consume'
                $url = "https://api.sandbox.midtrans.com/v2/" . $ord->order_id . "/status";

                // Open connection
                $ch = curl_init();

                // Set the url, number of GET vars, GET data
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, false);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

                $result = curl_exec($ch);

                curl_close($ch);
                $status = json_decode($result);
                // dd($status->status_code);
                if ($status->status_code == 200) {
                    $data = [
                        'status' => "Lunas"
                    ];
                    Http::get('https://wa.dlhcode.com/send-message?api_key=' . Helper::apk()->token_whatsapp . '&sender=' . Helper::apk()->tlp . '&number=' . $ord->no_tlp . '&message=Terima kasih, pembayaran anda berhasil pada bulan ');
                } elseif ($status->status_code == 201) {
                    $data = [
                        'status' => "Pending"
                    ];
                } else {
                    $data = [
                        'status' => "Failed"
                    ];
                }
                DB::table('payment')->where('order_id', $ord->order_id)->update($data);
            }
        }
        // foreach ($getOrderId as $ord) {
        //     if ($ord->order_id != null) {
        //         $getDataMidtrans = \Midtrans\Transaction::status($ord->order_id);
        //         if ($getDataMidtrans->status_code == 200) {
        //             $data = [
        //                 'status' => "Lunas"
        //             ];
        //             Http::get('https://wa.dlhcode.com/send-message?api_key=' . Helper::apk()->token_whatsapp . '&sender=' . Helper::apk()->tlp . '&number=' . $ord->no_tlp . '&message=Terima kasih, pembayaran anda berhasil pada bulan ');
        //         } elseif ($getDataMidtrans->status_code == 201) {
        //             $data = [
        //                 'status' => "Pending"
        //             ];
        //         } else {
        //             $data = [
        //                 'status' => "Failed"
        //             ];
        //         }
        //         DB::table('payment')->where('order_id', $ord->order_id)->update($data);
        //     }
        // }

        //Bulanan
        $cekBulanan = DB::select("SELECT p.tagihan_id, COUNT(p.status) as total FROM payment p LEFT JOIN users u on u.id=p.user_id WHERE u.nis = '$request->nis' AND p.status = 'Lunas' AND bulan_id is not null GROUP BY p.tagihan_id");
        foreach ($cekBulanan as $cb) {
            $data = [
                'status' => 'Lunas'
            ];
            if ($cb->total >= 12) {
                DB::table('tagihan')->where('id', $cb->tagihan_id)->update($data);
            }
        }
        $cekLainya = DB::select("SELECT p.tagihan_id, COUNT(p.status) as total, p.status FROM payment p LEFT JOIN users u on u.id=p.user_id WHERE u.nis = '$request->nis' AND p.status = 'Lunas' AND bulan_id is null GROUP BY p.tagihan_id, p.status");
        foreach ($cekLainya as $cb) {
            $data = [
                'status' => 'Lunas'
            ];
            if ($cb->total >= 1) {
                DB::table('tagihan')->where('id', $cb->tagihan_id)->update($data);
            }
        }
        $data['title'] = "Pembayaran";
        $data['getSiswa'] = DB::select("select * from users where role = '2'");
        $data['thajaran'] = DB::select("select * from tahun_ajaran where active = 'ON'");
        $data['kelas'] = DB::select("select * from kelas");
        $data['siswa'] = DB::table('users')->join('tagihan', 'users.id', '=', 'tagihan.user_id')->join('kelas', 'kelas.id', '=', 'tagihan.kelas_id')->where('users.nis', $request->nis)->where('users.kelas_id', $request->kelas_id)->first();
        $data['pembayaran_bulanan'] = DB::select("SELECT IF(COUNT(p.bulan_id) = 12, 'Lunas', 'Belum Lunas') as status_bayar,
        SUM(p.nilai) as total_bayar, t.thajaran_id, u.nis, ta.tahun, k.nama_kelas, jp.pembayaran, t.id
        FROM tagihan t  LEFT JOIN payment p on p.tagihan_id=t.id
        LEFT JOIN tahun_ajaran ta on ta.id=t.thajaran_id
        LEFT JOIN jenis_pembayaran jp on jp.id=t.jenis_pembayaran
        LEFT JOIN users u on u.id=t.user_id
        left join kelas k on k.id=t.kelas_id
        WHERE u.nis = '$request->nis'
        and t.jenis_pembayaran = '1'
        GROUP BY t.thajaran_id, u.nis, ta.tahun, jp.pembayaran, t.id");
        // dd($data['pembayaran_bulanan']);
        $data['pembayaran_lainya'] = DB::select("select t.*, u.nama_lengkap, k.nama_kelas, ta.tahun, jp.pembayaran, u.nis, p.order_id,
        p.pdf_url, p.metode_pembayaran, p.status as status_payment from tagihan t left join users u on t.user_id=u.id
        left join tahun_ajaran ta on ta.id=t.thajaran_id
        left join jenis_pembayaran jp on jp.id=t.jenis_pembayaran
        left join payment p on p.tagihan_id=t.id
        left join kelas k on k.id=t.kelas_id
        where u.nis = '$request->nis'
        and t.jenis_pembayaran != '1'");


        $params['activity']    = "Search Pembayaran";
        $params['detail']    = "Search Pembayaran Nis '$request->nis' dan kelas Id '$request->kelas_id'";
        Helper::log_transaction($params);
        // dd($data['pembayaran_lainya']);
        if ($data['pembayaran_bulanan'] || $data['pembayaran_lainya'] == true) {

            return view('backend.pembayaran.view', $data);
        } else {
            Alert::warning('Peringatan', 'SISWA BELUM ADA TAGIHAN');
            return view('backend.pembayaran.view', $data);
        }
    }



    public function spp($id_tagihan)
    {
        $getOrderId = DB::select("select p.*, u.nis, u.nama_lengkap, u.no_tlp, b.nama_bulan from users u left join payment p on p.user_id=u.id left join bulan b on b.id=p.bulan_id where u.nis = '" . request()->user()->nis . "' and p.status != 'Lunas' ORDER BY p.created_at DESC");
        // dd($getOrderId);
        if (isset($getOrderId)) {
            foreach ($getOrderId as $ord) {
                // dd($ord);

                $headers = array(
                    'Content-Type:application/json',
                    'Authorization: Basic ' . base64_encode(Helper::apk()->serverKey)
                );

                // the url of the API you are contacting to 'consume'
                $url = "https://api.sandbox.midtrans.com/v2/" . $ord->order_id . "/status";

                // Open connection
                $ch = curl_init();

                // Set the url, number of GET vars, GET data
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, false);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

                $result = curl_exec($ch);

                curl_close($ch);
                $status = json_decode($result);
                if ($status->status_code == 200) {
                    $data = [
                        'status' => "Lunas"
                    ];
                    Http::get('https://wa.dlhcode.com/send-message?api_key=' . Helper::apk()->token_whatsapp . '&sender=' . Helper::apk()->tlp . '&number=' . $ord->no_tlp . '&message=Terima kasih, pembayaran Bulan ' . $ord->nama_bulan . ' berhasil dengan nama siswa ' . $ord->nama_lengkap . ' nis ' . $ord->nis . '');
                } elseif ($status->status_code == 201) {
                    $data = [
                        'status' => "Pending"
                    ];
                    Http::get('https://wa.dlhcode.com/send-message?api_key=' . Helper::apk()->token_whatsapp . '&sender=' . Helper::apk()->tlp . '&number=' . $ord->no_tlp . '&message=Mohon Maaf, pembayaran Bulan ' . $ord->nama_bulan . ' Belum berhasil dengan nama siswa ' . $ord->nama_lengkap . ' nis ' . $ord->nis . '');
                } else {
                    $data = [
                        'status' => "Failed"
                    ];
                    Http::get('https://wa.dlhcode.com/send-message?api_key=' . Helper::apk()->token_whatsapp . '&sender=' . Helper::apk()->tlp . '&number=' . $ord->no_tlp . '&message=Mohon Maaf, pembayaran Bulan ' . $ord->nama_bulan . ' Gagal dengan nama siswa ' . $ord->nama_lengkap . ' nis ' . $ord->nis . '');
                }
                DB::table('payment')->where('order_id', $ord->order_id)->update($data);
            }
        }





        $data['title'] = "Riwayat Pembayaran Spp";
        // $data['id_tagihan'] = $id_tagihan;

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
        Http::get('https://wa.dlhcode.com/send-message?api_key=' . Helper::apk()->token_whatsapp . '&sender=' . Helper::apk()->tlp . '&number=' . $getusers->no_tlp . '&message=Terima kasih, pembayaran Bulanan anda berhasil dengan nama siswa ' . $getusers->nama_lengkap . ' dengan nis ' . $getusers->nis . '. Silahkan cek tagihan anda di dashboard siswa');
        DB::table('payment')->insert($data);
        $request->metode_pembayaran == "Manual" ? Alert::success('Success', 'Pembayaran Berhasil') : Alert::warning('Peringatan', 'Segera melakukan pembayaran!!!');
        return redirect("/pembayaran/spp/$request->tagihan_id");
    }
    public function payment($id_tagihan)
    {
        $data['title'] = "Payment";
        $data['payment'] = DB::select("SELECT t.*, u.nama_lengkap, jp.pembayaran, ta.tahun, u.nis FROM tagihan t LEFT JOIN users u on u.id=t.user_id LEFT JOIN jenis_pembayaran jp on jp.id=t.jenis_pembayaran LEFT JOIN tahun_ajaran ta on ta.id=t.thajaran_id WHERE t.id = '$id_tagihan'");
        // dd($data['payment']);
        return view('backend.pembayaran.payment', $data);
    }

    public function paymentAddProses(Request $request)
    {
        // dd($request->all());
        $dataMidtrans = json_decode($request->result_data);
        // dd();
        $data = [
            'user_id' => $request->user_id,
            'tagihan_id' => $request->tagihan_id,
            'kelas_id' => $request->kelas_id,
            'nilai' => str_replace(',', '', str_replace('Rp. ', '', $request->nilai)),
            'order_id' => isset($dataMidtrans->order_id) == false ? null : $dataMidtrans->order_id,
            'pdf_url' => isset($dataMidtrans->pdf_url) == false ? null : $dataMidtrans->pdf_url,
            'metode_pembayaran' => $request->metode_pembayaran,
            'status' => $request->metode_pembayaran == "Online" ? "Pending" : 'Lunas',
            'created_at' => now(),
        ];
        // dd($data);
        $getusers = DB::table('users')->where('id', $request->user_id)->first();
        Http::get('https://wa.dlhcode.com/send-message?api_key=' . Helper::apk()->token_whatsapp . '&sender=' . Helper::apk()->tlp . '&number=' . $getusers->no_tlp . '&message=Terima kasih, pembayaran dengan jumlah ' . $request->nilai . ' dengan nama siswa ' . $getusers->nama_lengkap . ' dengan nis ' . $getusers->nis . ' Berhasil. Silahkan cek tagihan anda di dashboard siswa');
        DB::table('payment')->insert($data);
        $request->metode_pembayaran == "Manual" ? Alert::success('Success', 'Pembayaran Berhasil') : Alert::warning('Peringatan', 'Segera melakukan pembayaran!!!');
        return redirect("/pembayaran/search?&kelas_id=$request->kelas_id&nis=$request->nis");
    }
    function siswaByKelas($kelas_id)
    {

        // dd($kelas_id);

        // $query = DB::table('users')->where('kelas_id', $kelas_id)->where('role', 2)->where('status', '!=', 'Lulus')->get();
        if ($kelas_id != "Lulus") {
            $query = DB::select("select * from users where status != 'Lulus' and ((role = '2' and kelas_id = '$kelas_id') or role = '3') order by role desc, nama_lengkap asc");
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
}
