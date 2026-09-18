<?php

namespace App\Modules\Setting\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    /** @var list<string> */
    protected $fillable = ['key', 'value'];

    /** Umur cache pengaturan dalam menit. */
    private const CACHE_TTL = 1440;

    /**
     * Ambil nilai pengaturan, dengan fallback bila belum pernah diisi.
     *
     * Hasilnya di-cache karena nilai ini dibaca pada setiap pencetakan nota
     * sementara isinya nyaris tidak pernah berubah.
     */
    public static function get(string $key, string $default = ''): string
    {
        return Cache::remember(
            static::cacheKey($key),
            now()->addMinutes(self::CACHE_TTL),
            static fn (): string => (string) (static::query()->where('key', $key)->value('value') ?? $default),
        );
    }

    public static function set(string $key, string $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);

        Cache::forget(static::cacheKey($key));
    }

    private static function cacheKey(string $key): string
    {
        return 'setting.' . $key;
    }
}
