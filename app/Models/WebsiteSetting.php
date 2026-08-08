<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class WebsiteSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * Ambil satu value berdasarkan key, dengan cache.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $settings = Cache::rememberForever('website_settings', function () {
            return self::all()->pluck('value', 'key');
        });

        return $settings[$key] ?? $default;
    }

    /**
     * Set / update value berdasarkan key, sekaligus refresh cache.
     */
    public static function set(string $key, mixed $value): void
    {
        self::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );

        Cache::forget('website_settings');
    }

    /**
     * Ambil semua settings sebagai array key => value (dengan cache).
     */
    public static function allSettings(): array
    {
        return Cache::rememberForever('website_settings', function () {
            return self::all()->pluck('value', 'key')->toArray();
        });
    }

    /**
     * Bersihkan cache secara manual kalau perlu.
     */
    public static function clearCache(): void
    {
        Cache::forget('website_settings');
    }

    /**
     * Otomatis clear cache setiap kali ada perubahan data lewat Eloquent
     * (misalnya dari Tinker atau seeder tanpa lewat method set()).
     */
    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('website_settings'));
        static::deleted(fn () => Cache::forget('website_settings'));
    }
}