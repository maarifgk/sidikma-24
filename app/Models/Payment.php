<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $table = 'payment';

    // Disable timestamps jika table payment tidak memiliki updated_at
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'tagihan_id',
        'kelas_id',
        'bulan_id',
        'nilai',
        'order_id',
        'pdf_url',
        'metode_pembayaran',
        'status',
        'created_at',
    ];

    protected $dates = [
        'created_at',
    ];

    /**
     * Relationship: Belongs to User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship: Belongs to Tagihan
     */
    public function tagihan()
    {
        return $this->belongsTo(Tagihan::class);
    }

    /**
     * Scope: Get pending payments
     */
    public function scopePending($query)
    {
        return $query->where('status', 'Pending');
    }

    /**
     * Scope: Get paid payments
     */
    public function scopeLunas($query)
    {
        return $query->where('status', 'Lunas');
    }

    /**
     * Scope: Get failed payments
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'Failed');
    }
}
