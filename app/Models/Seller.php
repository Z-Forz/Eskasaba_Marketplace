<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seller extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'whatsapp_number',
        'description',
        'status',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'approved_at' => 'datetime',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    // Pemilik toko
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Produk yang dijual
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    // Semua pesanan yang masuk ke seller
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}