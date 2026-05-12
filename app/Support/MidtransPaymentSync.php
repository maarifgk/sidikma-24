<?php

namespace App\Support;

use App\Providers\Helper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MidtransPaymentSync
{
    public static function serverKey(): ?string
    {
        return Helper::apk()->serverKey ?? config('midtrans.server_key');
    }

    public static function clientKey(): ?string
    {
        return Helper::apk()->clientKey ?? config('midtrans.client_key');
    }

    public static function isProduction(): bool
    {
        $serverKey = (string) static::serverKey();

        if (str_starts_with($serverKey, 'SB-Mid-server-')) {
            return false;
        }

        if (str_starts_with($serverKey, 'Mid-server-')) {
            return true;
        }

        return (bool) config('midtrans.is_production', true);
    }

    public static function baseUrl(): string
    {
        return static::isProduction()
            ? 'https://api.midtrans.com'
            : 'https://api.sandbox.midtrans.com';
    }

    public static function mapTransactionStatus(?string $transactionStatus, ?string $fraudStatus = null): string
    {
        $transactionStatus = strtolower((string) $transactionStatus);
        $fraudStatus = strtolower((string) $fraudStatus);

        if ($transactionStatus === 'capture') {
            return $fraudStatus === 'challenge' ? 'Pending' : 'Lunas';
        }

        if (in_array($transactionStatus, ['settlement'], true)) {
            return 'Lunas';
        }

        if (in_array($transactionStatus, ['pending', 'authorize'], true)) {
            return 'Pending';
        }

        if (in_array($transactionStatus, ['deny', 'cancel', 'expire', 'failure', 'refund', 'chargeback', 'partial_refund', 'partial_chargeback'], true)) {
            return 'Failed';
        }

        return 'Pending';
    }

    public static function fetchTransactionStatus(string $orderId): ?array
    {
        $serverKey = static::serverKey();

        if (!$serverKey || !$orderId) {
            return null;
        }

        try {
            $response = Http::acceptJson()
                ->withBasicAuth($serverKey, '')
                ->timeout(15)
                ->get(static::baseUrl() . '/v2/' . urlencode($orderId) . '/status');

            if (!$response->successful()) {
                Log::warning('Midtrans status request failed', [
                    'order_id' => $orderId,
                    'http_status' => $response->status(),
                    'response' => $response->body(),
                ]);

                return null;
            }

            return $response->json();
        } catch (\Throwable $e) {
            Log::warning('Midtrans status request exception', [
                'order_id' => $orderId,
                'message' => $e->getMessage(),
            ]);

            return null;
        }
    }

    public static function syncPaymentByOrderId(string $orderId, ?array $payload = null): ?string
    {
        if (!$orderId) {
            return null;
        }

        $payload = $payload ?: static::fetchTransactionStatus($orderId);

        if (!$payload) {
            return null;
        }

        $localStatus = static::mapTransactionStatus(
            $payload['transaction_status'] ?? null,
            $payload['fraud_status'] ?? null
        );

        $payments = DB::table('payment')
            ->select('id', 'tagihan_id', 'pdf_url')
            ->where('order_id', $orderId)
            ->get();

        if ($payments->isEmpty()) {
            Log::warning('Midtrans callback/payment sync order not found locally', [
                'order_id' => $orderId,
                'transaction_status' => $payload['transaction_status'] ?? null,
            ]);

            return $localStatus;
        }

        DB::table('payment')
            ->where('order_id', $orderId)
            ->update([
                'status' => $localStatus,
                'pdf_url' => $payload['pdf_url'] ?? DB::raw('pdf_url'),
            ]);

        static::refreshTagihanStatuses(
            $payments->pluck('tagihan_id')->filter()->unique()->values()->all()
        );

        return $localStatus;
    }

    public static function syncPendingPaymentsForNis(string $nis): void
    {
        static::syncPaymentsForNis($nis, ['Pending', 'Failed']);
    }

    public static function syncPaymentsForNis(string $nis, ?array $statuses = null): void
    {
        if (!$nis) {
            return;
        }

        $paymentsQuery = DB::table('payment as p')
            ->join('users as u', 'u.id', '=', 'p.user_id')
            ->where('u.nis', $nis)
            ->whereNotNull('p.order_id')
            ->select('p.order_id')
            ->distinct();

        if (!empty($statuses)) {
            $paymentsQuery->whereIn('p.status', $statuses);
        }

        $payments = $paymentsQuery->get();

        foreach ($payments as $payment) {
            static::syncPaymentByOrderId($payment->order_id);
        }
    }

    public static function syncTagihanStatusesForNis(string $nis): void
    {
        if (!$nis) {
            return;
        }

        $tagihanIds = DB::table('tagihan as t')
            ->join('users as u', 'u.id', '=', 't.user_id')
            ->where('u.nis', $nis)
            ->pluck('t.id')
            ->all();

        static::refreshTagihanStatuses($tagihanIds);
    }

    public static function refreshTagihanStatuses(array $tagihanIds): void
    {
        $tagihanIds = collect($tagihanIds)
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        if ($tagihanIds->isEmpty()) {
            return;
        }

        $tagihanRows = DB::table('tagihan')
            ->select('id', 'jenis_pembayaran')
            ->whereIn('id', $tagihanIds)
            ->get();

        foreach ($tagihanRows as $tagihan) {
            if ((int) $tagihan->jenis_pembayaran === 1) {
                $paidMonths = DB::table('payment')
                    ->where('tagihan_id', $tagihan->id)
                    ->where('status', 'Lunas')
                    ->whereNotNull('bulan_id')
                    ->distinct()
                    ->count('bulan_id');

                $status = $paidMonths >= 12 ? 'Lunas' : 'Belum Lunas';
            } else {
                $status = DB::table('payment')
                    ->where('tagihan_id', $tagihan->id)
                    ->where('status', 'Lunas')
                    ->exists() ? 'Lunas' : 'Belum Lunas';
            }

            DB::table('tagihan')
                ->where('id', $tagihan->id)
                ->update(['status' => $status]);
        }
    }
}
