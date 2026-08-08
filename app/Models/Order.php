<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'seller_id',
        'invoice_number',
        'total_price',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'total_price' => 'decimal:2',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    // Pembeli
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Penjual
    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }

    // Detail pesanan
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Pembayaran
    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    // Jadwal pengambilan
    public function pickupSchedule()
    {
        return $this->hasOne(PickupSchedule::class);
    }
}