<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SkTemplate extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'document_title',
        'description',
        'paper_size',
        'orientation',
        'builder_data',
        'custom_css',
        'content',
        'is_active',
    ];

    protected $casts = [
        'builder_data' => 'array',
        'is_active' => 'boolean',
    ];
}
