<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $username
 * @property string|null $nis_nip
 * @property string|null $email
 * @property string $password
 * @property bool $is_default_password
 * @property string $role
 * @property string|null $class_room
 * @property string|null $kelas
 * @property int $api_id
 * @property string|null $phone
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class User extends Authenticatable
{
    use HasFactory;

    protected $fillable = [
        'username',
        'nis_nip',
        'email',
        'password',
        'plain_password',
        'role',
        'class_room',
        'is_default_password',
        // Data profil dari API Sekolah
        'api_id',
        'phone',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_default_password' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saved(function (User $user) {
            if ($user->wasChanged('phone')) {
                Seller::where('user_id', $user->id)->update([
                    'whatsapp_number' => $user->phone,
                ]);
            }
        });
    }

    /**
     * Accessor alias agar $user->kelas merujuk ke $user->class_room
     */
    public function getKelasAttribute(): ?string
    {
        return $this->class_room;
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

    // Notifikasi sistem in-app
    public function notifications()
    {
        return $this->hasMany(Notification::class)->latest();
    }

    // Log Aktivitas & Riwayat Login
    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class)->latest();
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

    /**
     * Scope query user berdasarkan target penerima broadcast WhatsApp.
     * Only select users with non-empty phone number.
     */
    public function scopeByTargetRecipient(Builder $query, string $target): Builder
    {
        $query->whereNotNull('phone')
            ->where('phone', '!=', '');

        return match ($target) {
            'teacher' => $query->where('role', 'teacher'),

            'student_10' => $query->where('role', 'student')
                ->where(function ($q) {
                    $q->where('class_room', 'LIKE', 'X %')
                        ->orWhere('class_room', 'LIKE', 'X-%')
                        ->orWhere('class_room', 'LIKE', '10 %')
                        ->orWhere('class_room', 'LIKE', '10-%')
                        ->orWhere('class_room', 'LIKE', 'Kelas X%')
                        ->orWhere('class_room', 'LIKE', 'Kelas 10%')
                        ->orWhere('class_room', 'LIKE', 'Kls X%')
                        ->orWhere('class_room', 'LIKE', 'Kls 10%')
                        ->orWhereRaw("class_room REGEXP '^(kelas[[:space:]]+|kls[[:space:]]+)?(X|10)([^0-9a-zA-Z]|$)'");
                }),

            'student_11' => $query->where('role', 'student')
                ->where(function ($q) {
                    $q->where('class_room', 'LIKE', 'XI %')
                        ->orWhere('class_room', 'LIKE', 'XI-%')
                        ->orWhere('class_room', 'LIKE', '11 %')
                        ->orWhere('class_room', 'LIKE', '11-%')
                        ->orWhere('class_room', 'LIKE', 'Kelas XI%')
                        ->orWhere('class_room', 'LIKE', 'Kelas 11%')
                        ->orWhere('class_room', 'LIKE', 'Kls XI%')
                        ->orWhere('class_room', 'LIKE', 'Kls 11%')
                        ->orWhereRaw("class_room REGEXP '^(kelas[[:space:]]+|kls[[:space:]]+)?(XI|11)([^0-9a-zA-Z]|$)'");
                }),

            'student_12' => $query->where('role', 'student')
                ->where(function ($q) {
                    $q->where('class_room', 'LIKE', 'XII %')
                        ->orWhere('class_room', 'LIKE', 'XII-%')
                        ->orWhere('class_room', 'LIKE', '12 %')
                        ->orWhere('class_room', 'LIKE', '12-%')
                        ->orWhere('class_room', 'LIKE', 'Kelas XII%')
                        ->orWhere('class_room', 'LIKE', 'Kelas 12%')
                        ->orWhere('class_room', 'LIKE', 'Kls XII%')
                        ->orWhere('class_room', 'LIKE', 'Kls 12%')
                        ->orWhereRaw("class_room REGEXP '^(kelas[[:space:]]+|kls[[:space:]]+)?(XII|12)([^0-9a-zA-Z]|$)'");
                }),

            'all' => $query,
            default => $query,
        };
    }
}
