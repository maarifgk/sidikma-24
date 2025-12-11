<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataTenagaPendidik extends Model
{
    protected $table = 'data_tenaga_pendidik';

    protected $fillable = [
        'madrasah_id',
        'tahun_pelajaran',
        'kepala_guru_asn_sertifikasi',
        'kepala_guru_asn_non_sertifikasi',
        'kepala_guru_yayasan_sertifikasi_inpassing',
        'kepala_guru_yayasan_non_sertifikasi',
        'total',
    ];
}
