<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'variant_name',
        'quantity',
        'price',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
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

    // Produk
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}