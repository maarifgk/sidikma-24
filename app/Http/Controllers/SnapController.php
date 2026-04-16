<?php

namespace App\Http\Controllers;

use Exception;
use Midtrans\Snap;
use Midtrans\Config;
use App\Providers\Helper;
use Illuminate\Http\Request;

class SnapController extends Controller
{
    /**
     * Generate Snap Token untuk pembayaran ONLINE
     */
    public function payment(Request $request)
    {
        try {
            // 🔥 Ambil server key & client key dari database
            $serverKey  = Helper::apk()->serverKey;
            $clientKey  = Helper::apk()->clientKey;

            // 🔥 Jika kosong → langsung error biar tidak 401
            if (!$serverKey || !$clientKey) {
                return response()->json([
                    'error' => 'ServerKey / ClientKey tidak ditemukan di database'
                ], 500);
            }

            // 🔥 Set konfigurasi Midtrans
            Config::$serverKey     = $serverKey;
            Config::$isProduction  = true;  // ubah true kalau sudah live
            Config::$isSanitized   = true;
            Config::$is3ds         = true;

            // 🔥 Pastikan total adalah numeric
            $total = preg_replace('/[^\d]/', '', $request->total);

            if (!$total || $total < 1) {
                return response()->json([
                    'error' => 'Total pembayaran tidak valid'
                ], 500);
            }

            // -------------------------------------------
            //  DATA TRANSAKSI
            // -------------------------------------------
            $transaction_details = [
                'order_id' => uniqid(),
                'gross_amount' => (int) $total,
            ];

            $item_details = [[
                'id' => rand(1000, 9999),
                'price' => (int) $total,
                'quantity' => 1,
                'name' => $request->pembayaran ?? 'Pembayaran Sekolah',
            ]];

            $customer_details = [
                'first_name' => $request->nama_lengkap,
                'email'      => $request->email,
                'phone'      => $request->no_tlp,
            ];

            // Credit card options. If frontend requests installment, enable it.
            $credit_card = ['secure' => true];
            if (isset($request->installment) && ($request->installment == '1' || $request->installment === 'true')) {
                // Enable installment feature on Snap token request. Midtrans will show installment options
                // based on merchant settings and card/bank capabilities.
                $credit_card['installment'] = ['required' => true];
                // Optionally we could pass specific terms here if needed by Midtrans.
            }

            $transaction_data = [
                'transaction_details' => $transaction_details,
                'item_details' => $item_details,
                'customer_details' => $customer_details,
                'credit_card' => $credit_card,
                'expiry' => [
                    'start_time' => date('Y-m-d H:i:s O'),
                    'unit'       => 'minute',
                    'duration'   => 1440
                ]
            ];

            // 🔥 Request token ke Midtrans
            $snapToken = Snap::getSnapToken($transaction_data);

            return response()->json($snapToken);

        } catch (Exception $e) {

            // 🔥 Logging ke laravel.log
            \Log::error("MIDTRANS ERROR → " . $e->getMessage());

            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Untuk fitur lama / jika masih digunakan
     */
    public function token(Request $request)
    {
        return $this->payment($request);
    }
}
