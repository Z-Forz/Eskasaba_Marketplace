<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'icon',
        'description',
    ];

    /**
     * Check whether the category is newly created (within the last 7 days).
     */
    public function isNew(): bool
    {
        return $this->created_at && $this->created_at->greaterThanOrEqualTo(now()->subDays(7));
    }

    /**
     * Accessor for $category->is_new attribute.
     */
    public function getIsNewAttribute(): bool
    {
        return $this->isNew();
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    // Satu kategori memiliki banyak produk
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    // Satu kategori memiliki banyak ulasan melalui produk
    public function reviews()
    {
        return $this->hasManyThrough(Review::class, Product::class);
    }
}
