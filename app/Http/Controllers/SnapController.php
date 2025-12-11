<?php

namespace App\Http\Controllers;

use Exception;
use Midtrans\Snap;
use Midtrans\Config;
use App\Providers\Helper;
use App\Models\Payment;
use App\Veritrans\Veritrans;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SnapController extends Controller
{
    public function token(Request $request)
    {
        // Set your Merchant Server Key
        \Midtrans\Config::$serverKey = Helper::apk()->serverKey;
        // Set your Merchant Client Key
        \Midtrans\Config::$clientKey = Helper::apk()->clientKey;
        // Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
        \Midtrans\Config::$isProduction = true;
        // Set sanitization on (default)
        \Midtrans\Config::$isSanitized = true;
        // Set 3DS transaction for credit card to true
        \Midtrans\Config::$is3ds = true;


        // Required
        // Required
        // dd(request()->user()->nama_lengkap);
        $transaction_details = [
            'order_id' => rand(),
            'gross_amount' => $request->total, // no decimal allowed for creditcard
        ];

        // Optional
        $item1_details = [
            'id' => rand(000, 999),
            'price' => $request->total,
            'quantity' => 1,
            'name' => 'Pembayaran Spp',
        ];

        // Optional

        // Optional
        $item_details = [$item1_details];

        // Optional
        $billing_address = [
            'first_name' => request()->user()->nama_lengkap,
            'last_name' => 'a',
            'address' => 'a',
            'city' => 'a',
            'postal_code' => 'a',
            'phone' => 'a',
            'country_code' => 'IDN',
        ];

        // Optional
        $shipping_address = [
            'first_name' => request()->user()->nama_lengkap,
            'last_name' => 'Supriadi',
            'address' => 'Manggis 90',
            'city' => 'Jakarta',
            'postal_code' => '16601',
            'phone' => '08113366345',
            'country_code' => 'IDN',
        ];

        // Optional
        $customer_details = [
            'first_name' => request()->user()->nama_lengkap,
            'last_name' => '',
            'email' => request()->user()->email,
            'phone' => request()->user()->no_ortu,
            'billing_address' => $billing_address,
            'shipping_address' => $shipping_address,
        ];

        // Data yang akan dikirim untuk request redirect_url.
        $credit_card['secure'] = true;
        //ser save_card true to enable oneclick or 2click
        //$credit_card['save_card'] = true;

        $time = time();
        $custom_expiry = [
            'start_time' => date('Y-m-d H:i:s O', $time),
            'unit' => 'minute',
            'duration' => 1440,
        ];

        $transaction_data = [
            'transaction_details' => $transaction_details,
            'item_details' => $item_details,
            'customer_details' => $customer_details,
            'credit_card' => $credit_card,
            'expiry' => $custom_expiry,
        ];

        error_log(json_encode($transaction_data));
        $snapToken = \Midtrans\Snap::getSnapToken($transaction_data);
        error_log($snapToken);
        echo $snapToken;
    }
    public function payment(Request $request)
    {
        try {
            // Validasi input
            $request->validate([
                'user_id' => 'required|integer',
                'tagihan_id' => 'required|integer',
                'kelas_id' => 'required|integer',
                'total' => 'required|numeric|min:1',
                'pembayaran' => 'required|string',
            ]);

            // Set Midtrans Config
            \Midtrans\Config::$serverKey = Helper::apk()->serverKey;
            \Midtrans\Config::$clientKey = Helper::apk()->clientKey;
            \Midtrans\Config::$isProduction = (env('MIDTRANS_IS_PRODUCTION', false) == true);
            \Midtrans\Config::$isSanitized = true;
            \Midtrans\Config::$is3ds = true;

            // Generate unique order_id menggunakan timestamp + user_id + tagihan_id
            $order_id = 'ORDER-' . auth()->id() . '-' . $request->tagihan_id . '-' . time();

            // Simpan ke database terlebih dahulu dengan status pending
            $payment = Payment::create([
                'user_id' => $request->user_id,
                'tagihan_id' => $request->tagihan_id,
                'kelas_id' => $request->kelas_id,
                'nilai' => $request->total,
                'order_id' => $order_id,
                'metode_pembayaran' => 'Online',
                'status' => 'Pending',
            ]);

            // Data pembeli
            $user = auth()->user();
            $billing_address = [
                'first_name' => $user->nama_lengkap ?? 'Customer',
                'last_name' => '',
                'address' => $user->alamat ?? 'Alamat tidak ada',
                'city' => 'Indonesia',
                'postal_code' => '12345',
                'phone' => $user->no_tlp ?? '0812345678',
                'country_code' => 'IDN',
            ];

            // Detail transaksi untuk Midtrans
            $transaction_details = [
                'order_id' => $order_id,
                'gross_amount' => intval($request->total),
            ];

            $item_details = [
                [
                    'id' => $request->tagihan_id,
                    'price' => intval($request->total),
                    'quantity' => 1,
                    'name' => $request->pembayaran,
                ]
            ];

            $customer_details = [
                'first_name' => $user->nama_lengkap ?? 'Customer',
                'last_name' => '',
                'email' => $user->email ?? 'email@example.com',
                'phone' => $user->no_tlp ?? '0812345678',
                'billing_address' => $billing_address,
                'shipping_address' => $billing_address,
            ];

            $credit_card = [
                'secure' => true,
            ];

            $time = time();
            $custom_expiry = [
                'start_time' => date('Y-m-d H:i:s O', $time),
                'unit' => 'minute',
                'duration' => 1440,
            ];

            $transaction_data = [
                'transaction_details' => $transaction_details,
                'item_details' => $item_details,
                'customer_details' => $customer_details,
                'credit_card' => $credit_card,
                'expiry' => $custom_expiry,
            ];

            Log::info('Midtrans Transaction Data', ['data' => $transaction_data]);

            // Dapatkan Snap Token
            $snapToken = \Midtrans\Snap::getSnapToken($transaction_data);

            Log::info('Midtrans Snap Token Generated', ['order_id' => $order_id, 'token' => $snapToken]);

            return response()->json([
                'success' => true,
                'snap_token' => $snapToken,
                'order_id' => $order_id,
            ]);

        } catch (\Exception $e) {
            Log::error('Midtrans Payment Error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat token pembayaran: ' . $e->getMessage(),
            ], 500);
        }
    }
    public function finish(Request $request)
    {
        $result = json_decode($request->result_data);
        echo 'RESULT <br><pre>';
        var_dump($result);
        echo '</pre>';
    }
    public function callback(Request $request)
    {
        try {
            // Konfigurasi Midtrans
            \Midtrans\Config::$serverKey = Helper::apk()->serverKey;
            \Midtrans\Config::$isProduction = (env('MIDTRANS_IS_PRODUCTION', false) == true);
            \Midtrans\Config::$isSanitized = true;
            \Midtrans\Config::$is3ds = true;

            // Ambil notifikasi dari Midtrans
            $notif = new \Midtrans\Notification();

            // Log notification untuk debugging
            Log::info('Midtrans Callback Received', [
                'order_id' => $notif->order_id,
                'transaction_status' => $notif->transaction_status,
                'payment_type' => $notif->payment_type,
                'fraud_status' => $notif->fraud_status ?? 'N/A',
            ]);

            // Cek status transaksi dari notifikasi
            $transaction = $notif->transaction_status;
            $type = $notif->payment_type;
            $order_id = $notif->order_id;
            $fraud = $notif->fraud_status ?? null;

            // Mapping status Midtrans ke status aplikasi
            $status_mapping = [
                'capture' => 'Lunas',
                'settlement' => 'Lunas',
                'pending' => 'Pending',
                'deny' => 'Failed',
                'cancel' => 'Failed',
                'expire' => 'Failed',
            ];

            $payment_status = $status_mapping[$transaction] ?? 'Pending';

            // Update status pembayaran di tabel `payment`
            $payment = Payment::where('order_id', $order_id)->first();

            if ($payment) {
                // Jika status transaksi adalah capture/settlement dan fraud status bukan fraud
                if (in_array($transaction, ['capture', 'settlement'])) {
                    if ($fraud === 'accept' || $fraud === null) {
                        $payment->status = 'Lunas';
                        $payment->metode_pembayaran = $type;
                        $payment->save();

                        Log::info('Payment marked as LUNAS', ['order_id' => $order_id]);
                    } else if ($fraud === 'challenge') {
                        // Jika fraud status challenge, tetap pending
                        $payment->status = 'Pending';
                        $payment->metode_pembayaran = $type;
                        $payment->save();

                        Log::warning('Payment in challenge status', ['order_id' => $order_id]);
                    } else if ($fraud === 'deny') {
                        // Jika fraud status deny, mark as failed
                        $payment->status = 'Failed';
                        $payment->metode_pembayaran = $type;
                        $payment->save();

                        Log::error('Payment marked as FAILED due to fraud', ['order_id' => $order_id]);
                    }
                } else if ($transaction === 'pending') {
                    $payment->status = 'Pending';
                    $payment->metode_pembayaran = $type;
                    $payment->save();

                    Log::info('Payment still pending', ['order_id' => $order_id]);
                } else {
                    // Untuk status lain (deny, cancel, expire) mark as failed
                    $payment->status = 'Failed';
                    $payment->metode_pembayaran = $type;
                    $payment->save();

                    Log::error('Payment marked as FAILED', ['order_id' => $order_id, 'transaction_status' => $transaction]);
                }
            } else {
                Log::warning('Payment not found for order_id: ' . $order_id);
            }

            return response()->json(['message' => 'Callback received and handled'], 200);

        } catch (\Exception $e) {
            Log::error('Midtrans Callback Error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return response()->json(['message' => 'Error processing callback'], 500);
        }
    }
}
