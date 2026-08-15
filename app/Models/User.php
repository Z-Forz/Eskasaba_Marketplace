<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory;

    protected $fillable = [
        'username',
        'nis_nip',
        'email',
        'avatar',
        'password',
        'role',
        'is_default_password',
        // Data profil dari API Sekolah
        'api_id',
        'birth_date',
        'class',
        'major',
        'phone',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password'            => 'hashed',
            'is_default_password' => 'boolean',
            'birth_date'          => 'date',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    // Data seller (jika user mendaftar sebagai penjual)
    public function seller()
    {
        return $this->hasOne(Seller::class);
    }

    // Keranjang belanja
    public function cart()
    {
        return $this->hasOne(Cart::class);
    }

    // Semua pesanan
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // Review yang diberikan
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isSeller(): bool
    {
        return $this->seller()->exists();
    }

    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    public function isTeacher(): bool
    {
        return $this->role === 'teacher';
    }
}