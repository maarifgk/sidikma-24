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
        'custom_css',
        'content',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
