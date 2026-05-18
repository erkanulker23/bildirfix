<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class AdPlacement extends Model
{
    protected $fillable = [
        'key',
        'label',
        'type',
        'is_active',
        'adsense_slot',
        'media_url',
        'link_url',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public static function findActive(string $key): ?self
    {
        return Cache::remember('ad_placement:'.$key, 300, function () use ($key) {
            return self::query()
                ->where('key', $key)
                ->where('is_active', true)
                ->first();
        });
    }

    public static function flushCache(?string $key = null): void
    {
        if ($key !== null) {
            Cache::forget('ad_placement:'.$key);

            return;
        }

        foreach (array_keys((array) config('adsense.slots', [])) as $k) {
            Cache::forget('ad_placement:'.$k);
        }
    }
}
