<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PickupSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'pickup_date',
        'pickup_time',
        'is_picked_up',
        'picked_up_at',
    ];

    protected function casts(): array
    {
        return [
            'pickup_date' => 'date',
            'pickup_time' => 'datetime:H:i',
            'is_picked_up' => 'boolean',
            'picked_up_at' => 'datetime',
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