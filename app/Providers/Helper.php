<?php

namespace App\Providers;

use Request;
use Illuminate\Support\Facades\DB;


class Helper
{
    /**
     * Mendapatkan konfigurasi aplikasi dari database
     *
     * @return object
     */
    static public function apk()
    {
        $apk = DB::table('aplikasi')->first();

        // Jika belum ada data di database, return object kosong
        if (!$apk) {
            return (object) [
                'serverKey' => '',
                'clientKey' => '',
                'merchantId' => '',
                'token_whatsapp' => '',
                'tlp' => '',
            ];
        }

        return $apk;
    }

    /**
     * Log activity transaksi
     */
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
}


