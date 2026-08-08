<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_id',
        'product_id',
        'rating',
        'comment',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    // Pembeli yang memberikan review
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Pesanan terkait review ini
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Produk yang direview
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}