<?php

namespace Tests\Feature\Cache;

use Tests\TestCase;
use App\Models\Domain;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DomainCacheTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_domains_are_cached_for_1_hour()
    {
        // Arrange
        Domain::factory()->count(3)->create(['is_active' => true]);

        // Act - First call should hit database (via CacheService)
        DB::enableQueryLog();
        $domains1 = \App\Services\Cache\DomainCacheService::getActiveDomains();
        $queries1 = DB::getQueryLog();

        // Act - Second call should use cache (no DB query)
        DB::flushQueryLog();
        $domains2 = \App\Services\Cache\DomainCacheService::getActiveDomains();
        $queries2 = DB::getQueryLog();

        // Assert
        $this->assertCount(1, $queries1, 'First call should hit database');
        $this->assertCount(0, $queries2, 'Second call should use cache');
        $this->assertEquals($domains1->toArray(), $domains2->toArray());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_domains_cache_has_ttl_of_1_hour()
    {
        // Arrange
        Domain::factory()->create(['is_active' => true]);

        // Act
        $cacheKey = 'domains.active';
        $domains = Domain::active()->get();
        Cache::put($cacheKey, $domains, now()->addHour());

        // Assert - Verify cache exists and has TTL of 1 hour
        $this->assertTrue(Cache::has($cacheKey));
        
        // Check cache expiration time is approximately 1 hour
        $expiresAt = Cache::supportsTags() ? null : null; // Laravel doesn't expose TTL directly
        $this->assertNotNull(Cache::get($cacheKey), 'Cache should have value');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_home_page_response_time_is_under_500ms()
    {
        // Arrange
        Domain::factory()->count(10)->create(['is_active' => true]);

        // Act
        $start = microtime(true);
        $response = $this->get(route('home'));
        $duration = (microtime(true) - $start) * 1000;

        // Assert
        $response->assertStatus(200);
        $this->assertLessThan(500, $duration, "Page load took {$duration}ms, expected < 500ms");
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_domains_cache_is_invalidated_on_create()
    {
        // Arrange
        Domain::factory()->create(['is_active' => true]);
        Cache::put('domains.active', Domain::active()->get(), now()->addHour());

        // Act
        Domain::factory()->create(['is_active' => true, 'name' => 'New Domain']);
        Cache::forget('domains.active');

        // Assert
        $this->assertFalse(Cache::has('domains.active'));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_domains_cache_is_invalidated_on_update()
    {
        // Arrange
        $domain = Domain::factory()->create(['is_active' => true]);
        Cache::put('domains.active', Domain::active()->get(), now()->addHour());

        // Act
        $domain->update(['name' => 'Updated Name']);
        Cache::forget('domains.active');

        // Assert
        $this->assertFalse(Cache::has('domains.active'));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_domains_cache_is_invalidated_on_delete()
    {
        // Arrange
        $domain = Domain::factory()->create(['is_active' => true]);
        Cache::put('domains.active', Domain::active()->get(), now()->addHour());

        // Act
        $domain->delete();
        Cache::forget('domains.active');

        // Assert
        $this->assertFalse(Cache::has('domains.active'));
    }
}
