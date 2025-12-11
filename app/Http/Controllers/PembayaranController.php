<?php

namespace App\Http\Controllers;


use App\Providers\Helper;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use RealRashid\SweetAlert\Facades\Alert;
use Midtrans\Transaction;
use Midtrans\Config;

class PembayaranController extends Controller
{


    public function view()
    {
        $data['title'] = "Pembayaran";

        // Ambil semua siswa dengan role 2 dan 3 (untuk initial load jika diperlukan)
        $data['getSiswa'] = DB::table('users')
            ->whereIn('role', [2, 3])
            ->get();

        $data['thajaran'] = DB::table('tahun_ajaran')
            ->where('active', 'ON')
            ->get();

        $data['kelas'] = DB::table('kelas')->get();

        $data['siswa'] = "";
        $data['pembayaran_bulanan'] = "";
        $data['pembayaran_lainya'] = [];

        return view('backend.pembayaran.view', $data);
    }
    public function search(Request $request)
    {
        $user = auth()->user();

        // Gunakan data berdasarkan role
        if ($user->role == 3) {
            $nis = $user->nis;
            $user_id = $user->id;
            $kelas_id = $user->kelas_id;
        } else {
            $nis = $request->nis;
            $userData = DB::table('users')->where('nis', $nis)->first();
            if (!$userData) {
                Alert::warning('Peringatan', 'Data siswa tidak ditemukan');
                return redirect()->back();
            }
            $user_id = $userData->id;
            $kelas_id = $request->kelas_id;
        }

        $getOrderId = DB::select("
            SELECT p.*, u.nis, u.nama_lengkap, u.no_tlp
            FROM users u
            LEFT JOIN payment p ON p.user_id = u.id
            WHERE u.id = $user_id AND p.status = 'Pending'
            ORDER BY p.created_at DESC
        ");

        foreach ($getOrderId as $ord) {
            if ($ord->order_id != null) {
                $getDataMidtrans = \Midtrans\Transaction::status($ord->order_id);

                if ($getDataMidtrans->status_code == 200) {
                    $data = ['status' => "Lunas"];
                    Http::get('https://wa.dlhcode.com/send-message?api_key=' . Helper::apk()->token_whatsapp . '&sender=' . Helper::apk()->tlp . '&number=' . $ord->no_tlp . '&message=Terima kasih, pembayaran anda berhasil pada bulan ');
                } elseif ($getDataMidtrans->status_code == 201) {
                    $data = ['status' => "Pending"];
                } else {
                    $data = ['status' => "Failed"];
                }

                DB::table('payment')->where('order_id', $ord->order_id)->update($data);
            }
        }

        $cekBulanan = DB::select("
            SELECT p.tagihan_id, COUNT(p.status) as total
            FROM payment p
            WHERE p.user_id = $user_id
            AND p.status = 'Lunas'
            AND bulan_id IS NOT NULL
            GROUP BY p.tagihan_id
        ");

        foreach ($cekBulanan as $cb) {
            if ($cb->total >= 12) {
                DB::table('tagihan')->where('id', $cb->tagihan_id)->update(['status' => 'Lunas']);
            }
        }

        $cekLainya = DB::select("
            SELECT p.tagihan_id, COUNT(p.status) as total
            FROM payment p
            WHERE p.user_id = $user_id
            AND p.status = 'Lunas'
            AND bulan_id IS NULL
            GROUP BY p.tagihan_id
        ");

        foreach ($cekLainya as $cb) {
            if ($cb->total >= 1) {
                DB::table('tagihan')->where('id', $cb->tagihan_id)->update(['status' => 'Lunas']);
            }
        }

        $data['title'] = "Pembayaran";
        $data['getSiswa'] = DB::select("SELECT * FROM users WHERE role IN (2, 3)");
        $data['thajaran'] = DB::select("SELECT * FROM tahun_ajaran WHERE active = 'ON'");
        $data['kelas'] = DB::select("SELECT * FROM kelas");

        $data['siswa'] = DB::table('users')->where('id', $user_id)->first();

        $data['pembayaran_bulanan'] = DB::select("
            SELECT IF(COUNT(p.bulan_id) = 12, 'Lunas', 'Belum Lunas') as status_bayar,
                SUM(p.nilai) as total_bayar, t.thajaran_id, u.nis, ta.tahun, k.nama_kelas, jp.pembayaran, t.id
            FROM tagihan t
            LEFT JOIN payment p ON p.tagihan_id = t.id
            LEFT JOIN tahun_ajaran ta ON ta.id = t.thajaran_id
            LEFT JOIN jenis_pembayaran jp ON jp.id = t.jenis_pembayaran
            LEFT JOIN users u ON u.id = t.user_id
            LEFT JOIN kelas k ON k.id = t.kelas_id
            WHERE t.user_id = $user_id
            AND t.jenis_pembayaran = 1
            GROUP BY t.thajaran_id, u.nis, ta.tahun, jp.pembayaran, t.id
        ");

        $data['pembayaran_lainya'] = DB::select("
            SELECT t.*, t.k_iuran_2024, u.nama_lengkap, k.nama_kelas, ta.tahun, jp.pembayaran, u.nis, p.order_id,
                p.pdf_url, p.metode_pembayaran, p.status as status_payment
            FROM tagihan t
            LEFT JOIN users u ON t.user_id = u.id
            LEFT JOIN tahun_ajaran ta ON ta.id = t.thajaran_id
            LEFT JOIN jenis_pembayaran jp ON jp.id = t.jenis_pembayaran
            LEFT JOIN payment p ON p.tagihan_id = t.id
            LEFT JOIN kelas k ON k.id = t.kelas_id
            WHERE t.user_id = $user_id
            AND t.jenis_pembayaran != 1
        ");

        $params['activity'] = "Search Pembayaran";
        $params['detail'] = "Search Pembayaran user ID '$user_id' dan kelas Id '$kelas_id'";
        Helper::log_transaction($params);

        if ($data['pembayaran_bulanan'] || $data['pembayaran_lainya']) {
            return view('backend.pembayaran.view', $data);
        } else {
            Alert::warning('Peringatan', 'Maaf, Madrasah/Sekolah anda belum melunasi pembayaran periode januari-juni 2025, lakukan pembayaran dulu melalui akun mitra admin, agar bisa download SK Perpanjangan', 'Maaf, Madrasah/Sekolah anda belum melunasi pembayaran periode januari-juni 2025, lakukan pembayaran dulu melalui akun mitra admin, agar bisa download SK Perpanjangan', 'Terima Kasih');
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
                $url = "https://api.midtrans.com/v2/" . $ord->order_id . "/status";

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
                    Http::get('https://wa.dlhcode.com/send-message?api_key=' . Helper::apk()->token_whatsapp . '&sender=' . Helper::apk()->tlp . '&number=' . $ord->no_tlp . '&message=Terima kasih, pembayaran Bulan ' . $ord->nama_bulan . ' berhasil dengan nama ' . $ord->nama_lengkap . ' ewanugk ' . $ord->nis . '');
                } elseif ($status->status_code == 201) {
                    $data = [
                        'status' => "Pending"
                    ];
                    Http::get('https://wa.dlhcode.com/send-message?api_key=' . Helper::apk()->token_whatsapp . '&sender=' . Helper::apk()->tlp . '&number=' . $ord->no_tlp . '&message=Mohon Maaf, pembayaran Bulan ' . $ord->nama_bulan . ' Belum berhasil dengan nama ' . $ord->nama_lengkap . ' ewanugk ' . $ord->nis . '');
                } else {
                    $data = [
                        'status' => "Failed"
                    ];
                    Http::get('https://wa.dlhcode.com/send-message?api_key=' . Helper::apk()->token_whatsapp . '&sender=' . Helper::apk()->tlp . '&number=' . $ord->no_tlp . '&message=Mohon Maaf, pembayaran Bulan ' . $ord->nama_bulan . ' Gagal dengan nama ' . $ord->nama_lengkap . ' ewanugk ' . $ord->nis . '');
                }
                DB::table('payment')->where('order_id', $ord->order_id)->update($data);
            }
        }





        $data['title'] = "Riwayat Pembayaran";
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
        $params['activity']    = "Tambah Pembayaran";
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
                $url = "https://api.midtrans.com/v2/" . $ord->order_id . "/status";

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
                    Http::get('https://wa.dlhcode.com/send-message?api_key=' . Helper::apk()->token_whatsapp . '&sender=' . Helper::apk()->tlp . '&number=' . $ord->no_tlp . '&message=Terima kasih, pembayaran Bulan ' . $ord->nama_bulan . ' berhasil dengan nama ' . $ord->nama_lengkap . ' ewanugk ' . $ord->nis . '');
                } elseif ($status->status_code == 201) {
                    $data = [
                        'status' => "Pending"
                    ];
                    Http::get('https://wa.dlhcode.com/send-message?api_key=' . Helper::apk()->token_whatsapp . '&sender=' . Helper::apk()->tlp . '&number=' . $ord->no_tlp . '&message=Mohon Maaf, pembayaran Bulan ' . $ord->nama_bulan . ' Belum berhasil dengan nama ' . $ord->nama_lengkap . ' ewanugk ' . $ord->nis . '');
                } else {
                    $data = [
                        'status' => "Failed"
                    ];
                    Http::get('https://wa.dlhcode.com/send-message?api_key=' . Helper::apk()->token_whatsapp . '&sender=' . Helper::apk()->tlp . '&number=' . $ord->no_tlp . '&message=Mohon Maaf, pembayaran Bulan ' . $ord->nama_bulan . ' Gagal dengan nama ' . $ord->nama_lengkap . ' ewanugk ' . $ord->nis . '');
                }
                DB::table('payment')->where('order_id', $ord->order_id)->update($data);
            }
        }

        $data['title'] = "Payment";
        $data['payment'] = DB::select("SELECT t.*, u.nama_lengkap, jp.pembayaran, ta.tahun, u.nis FROM tagihan t LEFT JOIN users u on u.id=t.user_id LEFT JOIN jenis_pembayaran jp on jp.id=t.jenis_pembayaran LEFT JOIN tahun_ajaran ta on ta.id=t.thajaran_id WHERE t.id = '$id_tagihan'");
        // dd($data['payment']);
        return view('backend.pembayaran.payment', $data);
    }

    public function paymentAddProses(Request $request)
    {
        try {
            // Validasi input
            if (!$request->filled('result_data') && $request->metode_pembayaran === 'Online') {
                Alert::error('Error', 'Data pembayaran tidak lengkap');
                return redirect()->back();
            }

            $dataMidtrans = null;
            if ($request->filled('result_data')) {
                $dataMidtrans = json_decode($request->result_data);
            }

            // Cek apakah payment sudah ada untuk mencegah duplikasi
            $existingPayment = null;
            if ($dataMidtrans && isset($dataMidtrans->order_id)) {
                $existingPayment = DB::table('payment')
                    ->where('order_id', $dataMidtrans->order_id)
                    ->first();
            }

            // Jika sudah ada, jangan insert lagi
            if ($existingPayment) {
                Alert::warning('Peringatan', 'Pembayaran untuk transaksi ini sudah tercatat. Silahkan refresh halaman.');
                return redirect("/pembayaran/search?kelas_id=$request->kelas_id&nis=$request->nis");
            }

            $data = [
                'user_id' => $request->user_id,
                'tagihan_id' => $request->tagihan_id,
                'kelas_id' => $request->kelas_id,
                'nilai' => str_replace(['Rp. ', '.', ','], '', $request->nilai),
                'order_id' => $dataMidtrans && isset($dataMidtrans->order_id) ? $dataMidtrans->order_id : null,
                'pdf_url' => $dataMidtrans && isset($dataMidtrans->pdf_url) ? $dataMidtrans->pdf_url : null,
                'metode_pembayaran' => $request->metode_pembayaran,
                'status' => $request->metode_pembayaran == "Online" ? "Pending" : 'Lunas',
                'created_at' => now(),
            ];

            $getusers = DB::table('users')->where('id', $request->user_id)->first();

            if ($getusers) {
                // Kirim notifikasi WhatsApp (non-blocking)
                try {
                    \Illuminate\Support\Facades\Http::timeout(5)->get(
                        'https://wa.dlhcode.com/send-message',
                        [
                            'api_key' => Helper::apk()->token_whatsapp,
                            'sender' => Helper::apk()->tlp,
                            'number' => $getusers->no_tlp,
                            'message' => 'Terima kasih, pembayaran dengan jumlah ' . $request->nilai . ' dengan nama siswa ' . $getusers->nama_lengkap . ' dengan nis ' . $getusers->nis . ' Berhasil. Silahkan cek tagihan anda di dashboard siswa'
                        ]
                    );
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::warning('WhatsApp notification failed', ['error' => $e->getMessage()]);
                }
            }

            DB::table('payment')->insert($data);

            $message = $request->metode_pembayaran == "Manual"
                ? 'Pembayaran berhasil dicatat'
                : 'Pembayaran sedang diproses. Silahkan tunggu notifikasi dari bank Anda.';

            Alert::success('Success', $message);
            return redirect("/pembayaran/search?kelas_id=$request->kelas_id&nis=$request->nis");

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Payment Error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            Alert::error('Error', 'Terjadi kesalahan saat memproses pembayaran: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function iuran($id_tagihan)
    {
        $getOrderId = DB::select("select p.*, u.nis, u.nama_lengkap, u.no_tlp, b.nama_bulan from users u left join iuran p on p.user_id=u.id left join bulan b on b.id=p.bulan_id where u.nis = '" . request()->user()->nis . "' and p.status != 'Lunas' ORDER BY p.created_at DESC");
        // dd($getOrderId);
        if (isset($getOrderId)) {
            foreach ($getOrderId as $ord) {
                // dd($ord);

                $headers = array(
                    'Content-Type:application/json',
                    'Authorization: Basic ' . base64_encode(Helper::apk()->serverKey)
                );

                // the url of the API you are contacting to 'consume'
                $url = "https://api.midtrans.com/v2/" . $ord->order_id . "/status";

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
                    Http::get('https://wa.dlhcode.com/send-message?api_key=' . Helper::apk()->token_whatsapp . '&sender=' . Helper::apk()->tlp . '&number=' . $ord->no_tlp . '&message=Terima kasih, pembayaran Bulan ' . $ord->nama_bulan . ' berhasil dengan nama ' . $ord->nama_lengkap . ' ewanugk ' . $ord->nis . '');
                } elseif ($status->status_code == 201) {
                    $data = [
                        'status' => "Pending"
                    ];
                    Http::get('https://wa.dlhcode.com/send-message?api_key=' . Helper::apk()->token_whatsapp . '&sender=' . Helper::apk()->tlp . '&number=' . $ord->no_tlp . '&message=Mohon Maaf, pembayaran Bulan ' . $ord->nama_bulan . ' Belum berhasil dengan nama ' . $ord->nama_lengkap . ' ewanugk ' . $ord->nis . '');
                } else {
                    $data = [
                        'status' => "Failed"
                    ];
                    Http::get('https://wa.dlhcode.com/send-message?api_key=' . Helper::apk()->token_whatsapp . '&sender=' . Helper::apk()->tlp . '&number=' . $ord->no_tlp . '&message=Mohon Maaf, pembayaran Bulan ' . $ord->nama_bulan . ' Gagal dengan nama ' . $ord->nama_lengkap . ' ewanugk ' . $ord->nis . '');
                }
                DB::table('payment')->where('order_id', $ord->order_id)->update($data);
            }
        }

        $data['title'] = "Iuran";
        $data['iuran'] = DB::select("SELECT t.*, u.nama_lengkap, jp.pembayaran, ta.tahun, u.nis FROM tagihan t LEFT JOIN users u on u.id=t.user_id LEFT JOIN jenis_pembayaran jp on jp.id=t.jenis_pembayaran LEFT JOIN tahun_ajaran ta on ta.id=t.thajaran_id WHERE t.id = '$id_tagihan'");
        // dd($data['iuran']);
        return view('backend.pembayaran.iuran', $data);
    }

    public function iuranAddProses(Request $request)
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
    public function siswaByKelas($kelas_id)
    {
        if ($kelas_id != "Lulus") {
            // Jika yang dipilih bukan "Lulus", ambil berdasarkan kelas_id
            $query = DB::table('users')
                ->where('status', '!=', 'Lulus')
                ->where('kelas_id', $kelas_id)
                ->whereIn('role', [2, 3])
                ->get(['nis', 'nama_lengkap', 'role']);
        } else {
            // Jika yang dipilih adalah "Lulus", ambil semua yang status-nya "Lulus"
            $query = DB::table('users')
                ->where('status', '=', 'Lulus')
                ->whereIn('role', [2, 3])
                ->get(['nis', 'nama_lengkap', 'role']);
        }

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
