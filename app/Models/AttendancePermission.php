<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendancePermission extends Model
{
    protected $fillable = [
        'user_id',
        'kelas_id',
        'category',
        'start_date',
        'end_date',
        'reason',
        'attachment_path',
        'status',
        'reviewer_id',
        'reviewed_at',
        'review_notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'reviewed_at' => 'datetime',
    ];
}
