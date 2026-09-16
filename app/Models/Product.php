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
        'slug',
        'price',
        'stock',
        'description',
        'condition',
        'status',
        'discount',
        'variants',
    ];

    /**
     * Boot model events for automatic slug generation.
     */
    protected static function booted(): void
    {
        static::creating(function (Product $product) {
            if (empty($product->slug)) {
                $product->slug = static::generateUniqueSlug($product->name);
            }
        });

        static::updating(function (Product $product) {
            if ($product->isDirty('name') && empty($product->slug)) {
                $product->slug = static::generateUniqueSlug($product->name);
            }
        });
    }

    /**
     * Generate a unique slug for product.
     */
    public static function generateUniqueSlug(string $name): string
    {
        $baseSlug = \Illuminate\Support\Str::slug($name) ?: 'produk';
        $slug = $baseSlug . '-' . \Illuminate\Support\Str::lower(\Illuminate\Support\Str::random(6));

        while (static::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . \Illuminate\Support\Str::lower(\Illuminate\Support\Str::random(6));
        }

        return $slug;
    }

    /**
     * Use slug as default route key name for URLs.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Resolve route model binding by slug or fallback numeric ID.
     */
    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where($field ?? 'slug', $value)
            ->orWhere('id', $value)
            ->firstOrFail();
    }

    protected function casts(): array
    {
        return [
            'price'    => 'decimal:2',
            'discount' => 'decimal:2',
            'variants' => 'array',
        ];
    }

    /**
     * Cek apakah produk memiliki varian.
     */
    public function hasVariants(): bool
    {
        return ! empty($this->variants) && is_array($this->variants);
    }

    /**
     * Dapatkan harga minimum dari varian / base price.
     */
    public function getMinPrice(): float
    {
        if ($this->hasVariants()) {
            $prices = array_column($this->variants, 'price');
            if (! empty($prices)) {
                return (float) min($prices);
            }
        }

        return (float) $this->price;
    }

    /**
     * Dapatkan harga maksimum dari varian / base price.
     */
    public function getMaxPrice(): float
    {
        if ($this->hasVariants()) {
            $prices = array_column($this->variants, 'price');
            if (! empty($prices)) {
                return (float) max($prices);
            }
        }

        return (float) $this->price;
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