<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'admin_id',
        'event',
        'description',
        'ip_address',
        'user_agent',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    /**
     * Catat log aktivitas baru.
     */
    public static function record(?int $userId, string $event, string $description, ?Request $request = null, ?int $adminId = null): self
    {
        $req = $request ?? request();

        return self::create([
            'user_id'     => $userId,
            'admin_id'    => $adminId,
            'event'       => $event,
            'description' => $description,
            'ip_address'  => $req->ip() ?? '127.0.0.1',
            'user_agent'  => substr((string) $req->userAgent(), 0, 500),
        ]);
    }

    /**
     * Helper untuk parse user agent ke format ramah pengguna (misal: Chrome pada Windows).
     */
    public function getDeviceAttribute(): string
    {
        $agent = $this->user_agent ?? '';

        if (! $agent) {
            return 'Perangkat Tidak Dikenal';
        }

        $platform = 'Desktop';
        if (preg_match('/android/i', $agent)) {
            $platform = 'Android';
        } elseif (preg_match('/iphone|ipad|ipod/i', $agent)) {
            $platform = 'iOS / iPhone';
        } elseif (preg_match('/windows/i', $agent)) {
            $platform = 'Windows';
        } elseif (preg_match('/macintosh|mac os x/i', $agent)) {
            $platform = 'Mac OS';
        } elseif (preg_match('/linux/i', $agent)) {
            $platform = 'Linux';
        }

        $browser = 'Browser Web';
        if (preg_match('/chrome/i', $agent) && ! preg_match('/edg/i', $agent)) {
            $browser = 'Chrome';
        } elseif (preg_match('/firefox/i', $agent)) {
            $browser = 'Firefox';
        } elseif (preg_match('/safari/i', $agent) && ! preg_match('/chrome/i', $agent)) {
            $browser = 'Safari';
        } elseif (preg_match('/edg/i', $agent)) {
            $browser = 'Edge';
        }

        return "{$browser} ({$platform})";
    }

    /**
     * Helper icon berdasarkan jenis event.
     */
    public function getIconAttribute(): string
    {
        return match ($this->event) {
            'login'                => 'fa-solid fa-right-to-bracket text-emerald-600',
            'password_changed'     => 'fa-solid fa-key text-blue-600',
            'profile_updated'      => 'fa-solid fa-user-pen text-indigo-600',
            'password_reset_admin' => 'fa-solid fa-shield-halved text-amber-600',
            default                => 'fa-solid fa-circle-info text-slate-500',
        };
    }
}
