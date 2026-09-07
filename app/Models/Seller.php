<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seller extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'description',
        'reason',
        'products_plan',
        'rejection_note',
        'whatsapp_number',
        'qris_image',
        'status',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'approved_at' => 'datetime',
        ];
    }

    /**
     * Accessor untuk whatsapp_number agar selalu sinkron dengan phone milik User jika belum terisi.
     */
    public function getWhatsappNumberAttribute($value)
    {
        return $value ?: $this->user?->phone;
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function needsRevision(): bool
    {
        return $this->status === 'revision';
    }

    /**
     * Badge color untuk status di UI.
     */
    public function statusColor(): string
    {
        return match ($this->status) {
            'approved' => 'green',
            'rejected' => 'red',
            'revision' => 'yellow',
            default    => 'blue', // pending
        };
    }

    /**
     * Label status dalam Bahasa Indonesia.
     */
    public function statusLabel(): string
    {
        return match ($this->status) {
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            'revision' => 'Perlu Revisi',
            default    => 'Menunggu Verifikasi',
        };
    }
}