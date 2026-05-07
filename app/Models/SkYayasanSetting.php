<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SkYayasanSetting extends Model
{
    protected $fillable = [
        'periode_id',
        'nomor_pattern',
        'nomor_awal',
        'nomor_berikutnya',
        'digit_nomor',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
