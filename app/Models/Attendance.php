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
        'check_in_at',
        'check_in_latitude',
        'check_in_longitude',
        'check_in_gps_accuracy',
        'check_in_is_inside_geofence',
        'check_in_is_mock_location',
        'check_in_mock_detection_source',
        'check_in_selfie_path',
        'check_in_rejection_code',
        'check_in_rejection_reason',
        'check_in_device_info',
        'check_out_at',
        'check_out_latitude',
        'check_out_longitude',
        'check_out_gps_accuracy',
        'check_out_is_inside_geofence',
        'check_out_is_mock_location',
        'check_out_mock_detection_source',
        'check_out_selfie_path',
        'check_out_rejection_code',
        'check_out_rejection_reason',
        'check_out_device_info',
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'checked_at' => 'datetime',
        'latitude' => 'float',
        'longitude' => 'float',
        'gps_accuracy' => 'float',
        'is_inside_geofence' => 'boolean',
        'is_mock_location' => 'boolean',
        'check_in_at' => 'datetime',
        'check_in_latitude' => 'float',
        'check_in_longitude' => 'float',
        'check_in_gps_accuracy' => 'float',
        'check_in_is_inside_geofence' => 'boolean',
        'check_in_is_mock_location' => 'boolean',
        'check_out_at' => 'datetime',
        'check_out_latitude' => 'float',
        'check_out_longitude' => 'float',
        'check_out_gps_accuracy' => 'float',
        'check_out_is_inside_geofence' => 'boolean',
        'check_out_is_mock_location' => 'boolean',
    ];

    public function getCheckInTimeAttribute()
    {
        if ($this->check_in_at) {
            return $this->check_in_at;
        }

        return $this->check_type === 'datang' ? $this->checked_at : null;
    }

    public function getCheckOutTimeAttribute()
    {
        if ($this->check_out_at) {
            return $this->check_out_at;
        }

        return $this->check_type === 'pulang' ? $this->checked_at : null;
    }

    public function getLatestActivityAtAttribute()
    {
        return $this->check_out_time ?? $this->check_in_time ?? $this->checked_at;
    }

    public function getCheckInNoteAttribute(): ?string
    {
        if ($this->check_in_rejection_reason) {
            return $this->check_in_rejection_reason;
        }

        return $this->check_type === 'datang' ? $this->rejection_reason : null;
    }

    public function getCheckOutNoteAttribute(): ?string
    {
        if ($this->check_out_rejection_reason) {
            return $this->check_out_rejection_reason;
        }

        return $this->check_type === 'pulang' ? $this->rejection_reason : null;
    }

    public function getCombinedNoteAttribute(): ?string
    {
        return collect([$this->check_in_note, $this->check_out_note])
            ->filter()
            ->unique()
            ->implode(' | ') ?: null;
    }
}
