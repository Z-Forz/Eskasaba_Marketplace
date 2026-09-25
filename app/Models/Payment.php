<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'method',
        'amount',
        'proof',
        'status',
        'verified_at',
        'refund_proof',
        'refund_notes',
        'refunded_at',
        'buyer_confirmed_refund',
    ];

    protected function casts(): array
    {
        return [
            'amount'                 => 'decimal:2',
            'verified_at'            => 'datetime',
            'refunded_at'            => 'datetime',
            'buyer_confirmed_refund' => 'boolean',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    // Pesanan
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}