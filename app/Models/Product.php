<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_id',
        'category_id',
        'name',
        'price',
        'stock',
        'description',
        'condition',
        'status',
        'discount',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'discount' => 'decimal:2',
        ];
    }

    /**
     * Get final calculated price after discount (in Rupiah).
     */
    public function getFinalPriceAttribute(): float
    {
        $discount = (float) ($this->discount ?? 0);
        $price = (float) $this->price;

        if ($discount > 0) {
            return max(0, $price - $discount);
        }

        return $price;
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    // Penjual produk
    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }

    // Kategori produk
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Foto produk
    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    // Item keranjang
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    // Item pesanan
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Review produk
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}