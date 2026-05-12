<?php

namespace App\Providers;

use Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;


class Helper
{
    static public function apk()
    {
        $apk = DB::table('aplikasi')->first();
        // dd($apk);
        return $apk;
    }

    static public function log_transaction($params)
    {
        $data = [
            'user_id'    => request()->user()->id,
            'activity'  => empty($params['activity']) ? "" : $params['activity'],
            'ctime'     => date('Y-m-d H:i:s'),
            'ip'        => $_SERVER['REMOTE_ADDR'],
            'detail'    => !empty($params['detail']) ? $params['detail'] : "",
        ];

        $insert = DB::table('mm_logs')->insert($data);
        if (!$insert) {
            return false;
        }
        return true;
    }

    static public function sendWhatsappMessage(?string $number, ?string $message, ?string $apiKey = null, ?string $sender = null): bool
    {
        $number = trim((string) $number);
        $message = trim((string) $message);
        $apiKey = $apiKey ?: (static::apk()->token_whatsapp ?? null);
        $sender = $sender ?: (static::apk()->tlp ?? null);

        if ($number === '' || $message === '' || !$apiKey || !$sender) {
            return false;
        }

        try {
            $response = Http::timeout(10)
                ->connectTimeout(5)
                ->retry(1, 250)
                ->get('https://wa.dlhcode.com/send-message', [
                    'api_key' => $apiKey,
                    'sender' => $sender,
                    'number' => $number,
                    'message' => $message,
                ]);

            if ($response->failed()) {
                Log::warning('WhatsApp gateway returned non-success response', [
                    'number' => $number,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return false;
            }

            return true;
        } catch (\Throwable $e) {
            Log::warning('WhatsApp gateway request failed', [
                'number' => $number,
                'message' => $e->getMessage(),
            ]);

            return false;
        }
    }
}

