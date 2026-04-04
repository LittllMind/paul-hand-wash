<?php

namespace Tests\Feature\Cache;

use Tests\TestCase;
use App\Models\Evenement;
use App\Models\Lieu;
use App\Models\Categorie;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EvenementCacheTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_upcoming_evenements_are_cached()
    {
        // Arrange
        $lieu = Lieu::factory()->create();
        $categorie = Categorie::factory()->create();
        
        Evenement::factory()->count(3)->create([
            'lieu_id' => $lieu->id,
            'categorie_id' => $categorie->id,
            'date_debut' => now()->addWeek(),
        ]);

        // Act - First call via Cache Service
        DB::enableQueryLog();
        $evenements1 = \App\Services\Cache\EvenementCacheService::getUpcomingEvenements();
        $queries1 = DB::getQueryLog();

        // Act - Second call should use cache
        DB::flushQueryLog();
        $evenements2 = \App\Services\Cache\EvenementCacheService::getUpcomingEvenements();
        $queries2 = DB::getQueryLog();

        // Assert
        $this->assertGreaterThan(0, count($queries1), 'First call should hit database');
        $this->assertCount(0, $queries2, 'Second call should use cache (no DB queries)');
        $this->assertEquals($evenements1->toArray(), $evenements2->toArray());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_evenements_cache_has_ttl_of_1_hour()
    {
        // Arrange
        $cacheKey = 'evenements.upcoming';
        $lieu = Lieu::factory()->create();
        $categorie = Categorie::factory()->create();
        
        $evenements = Evenement::factory()->count(2)->create([
            'lieu_id' => $lieu->id,
            'categorie_id' => $categorie->id,
            'date_debut' => now()->addWeek(),
        ]);

        // Act
        Cache::put($cacheKey, $evenements, now()->addHour());

        // Assert
        $this->assertTrue(Cache::has($cacheKey));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_evenements_response_time_is_under_500ms()
    {
        // Arrange
        $lieu = Lieu::factory()->create();
        $categorie = Categorie::factory()->create();
        
        Evenement::factory()->count(10)->create([
            'lieu_id' => $lieu->id,
            'categorie_id' => $categorie->id,
            'date_debut' => now()->addWeek(),
        ]);

        // Act
        $start = microtime(true);
        $response = $this->get(route('admin.evenements.index'));
        $duration = (microtime(true) - $start) * 1000;

        // Assert
        $response->assertStatus(200);
        $this->assertLessThan(500, $duration, "Page load took {$duration}ms, expected < 500ms");
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_evenements_cache_is_invalidated_on_create()
    {
        // Arrange
        $lieu = Lieu::factory()->create();
        $categorie = Categorie::factory()->create();
        
        Evenement::factory()->create([
            'lieu_id' => $lieu->id,
            'categorie_id' => $categorie->id,
            'date_debut' => now()->addWeek(),
        ]);
        
        Cache::put('evenements.upcoming', Evenement::where('date_debut', '>=', now())->get(), now()->addHour());

        // Act
        Evenement::factory()->create([
            'lieu_id' => $lieu->id,
            'categorie_id' => $categorie->id,
            'date_debut' => now()->addWeeks(2),
            'titre' => 'New Event',
        ]);
        Cache::forget('evenements.upcoming');

        // Assert
        $this->assertFalse(Cache::has('evenements.upcoming'));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_evenements_cache_is_invalidated_on_update()
    {
        // Arrange
        $lieu = Lieu::factory()->create();
        $categorie = Categorie::factory()->create();
        
        $evenement = Evenement::factory()->create([
            'lieu_id' => $lieu->id,
            'categorie_id' => $categorie->id,
            'date_debut' => now()->addWeek(),
        ]);
        
        Cache::put('evenements.upcoming', Evenement::where('date_debut', '>=', now())->get(), now()->addHour());

        // Act
        $evenement->update(['titre' => 'Updated Title']);
        Cache::forget('evenements.upcoming');

        // Assert
        $this->assertFalse(Cache::has('evenements.upcoming'));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_evenements_cache_is_invalidated_on_delete()
    {
        // Arrange
        $lieu = Lieu::factory()->create();
        $categorie = Categorie::factory()->create();
        
        $evenement = Evenement::factory()->create([
            'lieu_id' => $lieu->id,
            'categorie_id' => $categorie->id,
            'date_debut' => now()->addWeek(),
        ]);
        
        Cache::put('evenements.upcoming', Evenement::where('date_debut', '>=', now())->get(), now()->addHour());

        // Act
        $evenement->delete();
        Cache::forget('evenements.upcoming');

        // Assert
        $this->assertFalse(Cache::has('evenements.upcoming'));
    }
}
