<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SellerRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_id',
        'type',
        'title',
        'description',
        'status',
        'is_read',
        'read_at',
        'admin_response',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'is_read'      => 'boolean',
            'read_at'      => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function typeLabel(): string
    {
        return match ($this->type) {
            'category' => 'Kategori Produk',
            'feature'  => 'Usulan Fitur',
            default    => 'Lainnya',
        };
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'completed' => 'Disetujui & Selesai',
            'rejected'  => 'Ditolak',
            default     => 'Menunggu Tinjauan Admin',
        };
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            'completed' => 'green',
            'rejected'  => 'red',
            default     => 'blue',
        };
    }
}
