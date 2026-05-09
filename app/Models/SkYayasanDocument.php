<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SkYayasanDocument extends Model
{
    protected $fillable = [
        'user_id',
        'sk_template_id',
        'tahun_sk',
        'original_filename',
        'stored_filename',
        'file_path',
        'mime_type',
        'file_size',
        'source_type',
        'matched_by',
        'uploaded_by',
    ];
}
