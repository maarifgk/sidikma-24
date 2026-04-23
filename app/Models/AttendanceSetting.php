<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceSetting extends Model
{
    protected $fillable = [
        'kelas_id',
        'enable_check_in',
        'enable_check_out',
        'enable_permission',
        'geofence_polygon',
        'check_in_time',
        'check_out_time',
        'late_tolerance_minutes',
        'max_gps_accuracy',
        'enable_fake_gps_detection',
        'require_selfie',
    ];

    protected $casts = [
        'enable_check_in' => 'boolean',
        'enable_check_out' => 'boolean',
        'enable_permission' => 'boolean',
        'geofence_polygon' => 'array',
        'max_gps_accuracy' => 'float',
        'enable_fake_gps_detection' => 'boolean',
        'require_selfie' => 'boolean',
    ];
}
