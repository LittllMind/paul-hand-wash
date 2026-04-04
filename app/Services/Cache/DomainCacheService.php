<?php

namespace App\Services\Cache;

use App\Models\Domain;
use Illuminate\Support\Facades\Cache;

class DomainCacheService
{
    public const CACHE_KEY = 'domains.active';
    public const TTL_HOURS = 1;

    /**
     * Get active domains from cache or database.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getActiveDomains()
    {
        return Cache::remember(self::CACHE_KEY, now()->addHours(self::TTL_HOURS), function () {
            return Domain::active()->get();
        });
    }

    /**
     * Clear the domains cache.
     *
     * @return bool
     */
    public static function clear(): bool
    {
        return Cache::forget(self::CACHE_KEY);
    }

    /**
     * Refresh the domains cache.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function refresh()
    {
        self::clear();
        return self::getActiveDomains();
    }
}
