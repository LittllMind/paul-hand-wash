<?php

namespace App\Services\Cache;

use App\Models\Evenement;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class EvenementCacheService
{
    public const CACHE_KEY_UPCOMING = 'evenements.upcoming';
    public const CACHE_KEY_ALL = 'evenements.all';
    public const TTL_HOURS = 1;

    /**
     * Get upcoming events from cache or database.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getUpcomingEvenements()
    {
        return Cache::remember(self::CACHE_KEY_UPCOMING, now()->addHours(self::TTL_HOURS), function () {
            return Evenement::where('date_debut', '>=', now())
                ->with(['lieu', 'categorie'])
                ->orderBy('date_debut')
                ->get();
        });
    }

    /**
     * Get all events from cache or database.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getAllEvenements()
    {
        return Cache::remember(self::CACHE_KEY_ALL, now()->addHours(self::TTL_HOURS), function () {
            return Evenement::with(['lieu', 'categorie'])
                ->orderBy('date_debut', 'desc')
                ->get();
        });
    }

    /**
     * Clear all evenement caches.
     *
     * @return bool
     */
    public static function clear(): bool
    {
        $result1 = Cache::forget(self::CACHE_KEY_UPCOMING);
        $result2 = Cache::forget(self::CACHE_KEY_ALL);
        
        return $result1 || $result2;
    }

    /**
     * Refresh the evenement caches.
     *
     * @return array
     */
    public static function refresh(): array
    {
        self::clear();
        return [
            'upcoming' => self::getUpcomingEvenements(),
            'all' => self::getAllEvenements(),
        ];
    }
}
