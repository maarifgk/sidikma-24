<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'user_id',
        'kelas_id',
        'attendance_date',
        'check_type',
        'status',
        'checked_at',
        'latitude',
        'longitude',
        'gps_accuracy',
        'is_inside_geofence',
        'is_mock_location',
        'mock_detection_source',
        'selfie_path',
        'rejection_code',
        'rejection_reason',
        'device_info',
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'checked_at' => 'datetime',
        'latitude' => 'float',
        'longitude' => 'float',
        'gps_accuracy' => 'float',
        'is_inside_geofence' => 'boolean',
        'is_mock_location' => 'boolean',
    ];
}
