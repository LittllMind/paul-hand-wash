<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Cache\DomainCacheService;
use App\Services\Cache\EvenementCacheService;
use Illuminate\Support\Facades\Cache;

class CacheManageCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:manage 
                            {action : clear|refresh|status}
                            {--type= : domains|evenements|all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manage application cache for domains and evenements';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $action = $this->argument('action');
        $type = $this->option('type') ?? 'all';

        return match ($action) {
            'clear' => $this->clearCache($type),
            'refresh' => $this->refreshCache($type),
            'status' => $this->showStatus(),
            default => $this->error("Action '{$action}' not recognized. Use: clear, refresh, status"),
        };
    }

    /**
     * Clear cache for specified type.
     */
    protected function clearCache(string $type): int
    {
        $cleared = [];

        if (in_array($type, ['domains', 'all'])) {
            DomainCacheService::clear();
            $cleared[] = 'domains';
        }

        if (in_array($type, ['evenements', 'all'])) {
            EvenementCacheService::clear();
            $cleared[] = 'evenements';
        }

        if (empty($cleared)) {
            $this->error("Invalid type '{$type}'. Use: domains, evenements, or all");
            return 1;
        }

        $this->info('Cache cleared for: ' . implode(', ', $cleared));
        return 0;
    }

    /**
     * Refresh cache for specified type.
     */
    protected function refreshCache(string $type): int
    {
        $refreshed = [];

        if (in_array($type, ['domains', 'all'])) {
            $domains = DomainCacheService::refresh();
            $refreshed[] = 'domains (' . $domains->count() . ' items)';
        }

        if (in_array($type, ['evenements', 'all'])) {
            $result = EvenementCacheService::refresh();
            $count = $result['upcoming']->count() + $result['all']->count();
            $refreshed[] = 'evenements (' . $count . ' items)';
        }

        if (empty($refreshed)) {
            $this->error("Invalid type '{$type}'. Use: domains, evenements, or all");
            return 1;
        }

        $this->info('Cache refreshed for: ' . implode(', ', $refreshed));
        return 0;
    }

    /**
     * Show cache status.
     */
    protected function showStatus(): int
    {
        $this->newLine();
        $this->info('=== Cache Status ===');
        $this->newLine();

        // Domains cache
        $domainsCached = Cache::has(DomainCacheService::CACHE_KEY);
        $this->line('Domains Cache: ' . ($domainsCached ? '✓ PRESENT' : '✗ Empty'));
        if ($domainsCached) {
            $domains = Cache::get(DomainCacheService::CACHE_KEY);
            $this->line('  - Items: ' . $domains->count());
        }

        $this->newLine();

        // Evenements cache
        $evenementsUpcomingCached = Cache::has(EvenementCacheService::CACHE_KEY_UPCOMING);
        $evenementsAllCached = Cache::has(EvenementCacheService::CACHE_KEY_ALL);
        $this->line('Evenements Cache:');
        $this->line('  - Upcoming: ' . ($evenementsUpcomingCached ? '✓ PRESENT' : '✗ Empty'));
        $this->line('  - All: ' . ($evenementsAllCached ? '✓ PRESENT' : '✗ Empty'));
        if ($evenementsUpcomingCached) {
            $evenements = Cache::get(EvenementCacheService::CACHE_KEY_UPCOMING);
            $this->line('  - Upcoming items: ' . $evenements->count());
        }

        $this->newLine();

        return 0;
    }
}
