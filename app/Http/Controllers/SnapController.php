<?php

namespace App\Http\Controllers;

use Exception;
use App\Support\MidtransPaymentSync;
use Midtrans\Snap;
use Midtrans\Config;
use App\Providers\Helper;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SnapController extends Controller
{
    protected function buildOrderId(Request $request): string
    {
        $userId = preg_replace('/\D+/', '', (string) $request->input('user_id', '0')) ?: '0';
        $tagihanId = preg_replace('/\D+/', '', (string) $request->input('tagihan_id', '0')) ?: '0';

        return sprintf(
            'ORD-%s-%s-%s',
            $userId,
            $tagihanId,
            Str::upper(Str::random(10))
        );
    }

    protected function createPendingPaymentRecord(Request $request, string $orderId, int $total): void
    {
        $tagihanId = (int) $request->input('tagihan_id');
        $userId = (int) $request->input('user_id');

        if (!$tagihanId || !$userId) {
            return;
        }

        $tagihan = DB::table('tagihan')
            ->select('id', 'user_id', 'kelas_id')
            ->where('id', $tagihanId)
            ->where('user_id', $userId)
            ->first();

        if (!$tagihan) {
            return;
        }

        $alreadyExists = DB::table('payment')
            ->where('order_id', $orderId)
            ->where('user_id', $userId)
            ->where('tagihan_id', $tagihanId)
            ->exists();

        if ($alreadyExists) {
            return;
        }

        DB::table('payment')->insert([
            'user_id' => $userId,
            'tagihan_id' => $tagihanId,
            'kelas_id' => $tagihan->kelas_id,
            'nilai' => $total,
            'order_id' => $orderId,
            'metode_pembayaran' => 'Online',
            'status' => 'Pending',
            'created_at' => now(),
        ]);

        MidtransPaymentSync::refreshTagihanStatuses([$tagihanId]);
    }

    /**
     * Generate Snap Token untuk pembayaran ONLINE
     */
    public function payment(Request $request)
    {
        try {
            // 🔥 Ambil server key & client key dari database
            $serverKey  = MidtransPaymentSync::serverKey();
            $clientKey  = MidtransPaymentSync::clientKey();

            // 🔥 Jika kosong → langsung error biar tidak 401
            if (!$serverKey || !$clientKey) {
                return response()->json([
                    'error' => 'ServerKey / ClientKey tidak ditemukan di database'
                ], 500);
            }

            // 🔥 Set konfigurasi Midtrans
            Config::$serverKey     = $serverKey;
            Config::$isProduction  = MidtransPaymentSync::isProduction();
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
                'order_id' => $this->buildOrderId($request),
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

            $transaction_data = [
                'transaction_details' => $transaction_details,
                'item_details' => $item_details,
                'customer_details' => $customer_details,
                'credit_card' => ['secure' => true],
                'expiry' => [
                    'start_time' => date('Y-m-d H:i:s O'),
                    'unit'       => 'minute',
                    'duration'   => 1440
                ]
            ];

            // 🔥 Request token ke Midtrans
            $snapToken = Snap::getSnapToken($transaction_data);
            $this->createPendingPaymentRecord($request, $transaction_details['order_id'], (int) $total);

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

    public function callback(Request $request)
    {
        $payload = $request->json()->all() ?: $request->all();

        $orderId = $payload['order_id'] ?? null;
        $statusCode = $payload['status_code'] ?? null;
        $grossAmount = $payload['gross_amount'] ?? null;
        $signatureKey = $payload['signature_key'] ?? null;
        $serverKey = MidtransPaymentSync::serverKey();

        if (!$orderId || !$statusCode || !$grossAmount || !$signatureKey || !$serverKey) {
            Log::warning('Midtrans callback payload incomplete', ['payload' => $payload]);

            return response()->json(['message' => 'Invalid payload'], 422);
        }

        $expectedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        if (!hash_equals($expectedSignature, $signatureKey)) {
            Log::warning('Midtrans callback signature mismatch', ['order_id' => $orderId]);

            return response()->json(['message' => 'Invalid signature'], 403);
        }

        Log::info('Midtrans callback received', [
            'order_id' => $orderId,
            'transaction_status' => $payload['transaction_status'] ?? null,
            'payment_type' => $payload['payment_type'] ?? null,
        ]);

        DB::beginTransaction();

        try {
            $status = MidtransPaymentSync::syncPaymentByOrderId($orderId, $payload);
            DB::commit();

            Log::info('Midtrans callback processed', [
                'order_id' => $orderId,
                'local_status' => $status,
            ]);

            return response()->json([
                'message' => 'Notification processed',
                'status' => $status,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Midtrans callback processing failed', [
                'order_id' => $orderId,
                'message' => $e->getMessage(),
            ]);

            return response()->json(['message' => 'Internal server error'], 500);
        }
    }
}
