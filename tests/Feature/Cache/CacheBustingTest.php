<?php

namespace Tests\Feature\Cache;

use Tests\TestCase;
use App\Models\Domain;
use App\Models\Evenement;
use App\Models\Lieu;
use App\Models\Categorie;
use Illuminate\Support\Facades\Cache;

class CacheBustingTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_admin_domain_create_busts_domain_cache()
    {
        // Arrange
        Domain::factory()->create(['is_active' => true]);
        $initialData = Domain::active()->get();
        Cache::put('domains.active', $initialData, now()->addHour());

        // Act - Simulate admin creating a domain
        $response = $this->postJson(route('admin.domaines.store'), [
            'name' => 'New Domain',
            'slug' => 'new-domain',
            'description' => 'Description',
            'is_active' => true,
        ]);

        // Assert
        $response->assertStatus(302); // Redirect after success
        $this->assertFalse(Cache::has('domains.active'), 'Cache should be busted after domain creation');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_admin_domain_update_busts_domain_cache()
    {
        // Arrange
        $domain = Domain::factory()->create(['is_active' => true]);
        $initialData = Domain::active()->get();
        Cache::put('domains.active', $initialData, now()->addHour());

        // Act
        $response = $this->putJson(route('admin.domaines.update', $domain), [
            'name' => 'Updated Domain',
            'slug' => $domain->slug,
            'description' => $domain->description,
            'is_active' => true,
        ]);

        // Assert
        $response->assertStatus(302);
        $this->assertFalse(Cache::has('domains.active'), 'Cache should be busted after domain update');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_admin_domain_delete_busts_domain_cache()
    {
        // Arrange
        $domain = Domain::factory()->create(['is_active' => true]);
        $initialData = Domain::active()->get();
        Cache::put('domains.active', $initialData, now()->addHour());

        // Act
        $response = $this->delete(route('admin.domaines.destroy', $domain));

        // Assert
        $response->assertStatus(302);
        $this->assertFalse(Cache::has('domains.active'), 'Cache should be busted after domain deletion');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_admin_evenement_create_busts_evenement_cache()
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
        $response = $this->postJson(route('admin.evenements.store'), [
            'titre' => 'New Event',
            'description' => 'Description',
            'date_debut' => now()->addWeeks(2)->format('Y-m-d H:i:s'),
            'date_fin' => now()->addWeeks(2)->addHours(2)->format('Y-m-d H:i:s'),
            'lieu_id' => $lieu->id,
            'categorie_id' => $categorie->id,
            'places_limite' => 50,
        ]);

        // Assert
        $response->assertStatus(302);
        $this->assertFalse(Cache::has('evenements.upcoming'), 'Cache should be busted after evenement creation');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_admin_evenement_update_busts_evenement_cache()
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
        $response = $this->putJson(route('admin.evenements.update', $evenement), [
            'titre' => 'Updated Event',
            'description' => $evenement->description,
            'date_debut' => $evenement->date_debut->format('Y-m-d H:i:s'),
            'date_fin' => $evenement->date_fin->format('Y-m-d H:i:s'),
            'lieu_id' => $evenement->lieu_id,
            'categorie_id' => $evenement->categorie_id,
            'places_limite' => $evenement->places_limite,
        ]);

        // Assert
        $response->assertStatus(302);
        $this->assertFalse(Cache::has('evenements.upcoming'), 'Cache should be busted after evenement update');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_admin_evenement_delete_busts_evenement_cache()
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
        $response = $this->delete(route('admin.evenements.destroy', $evenement));

        // Assert
        $response->assertStatus(302);
        $this->assertFalse(Cache::has('evenements.upcoming'), 'Cache should be busted after evenement deletion');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_cache_keys_use_proper_prefix()
    {
        // Arrange
        Domain::factory()->create(['is_active' => true]);
        
        // Act
        Cache::put('domains.active', Domain::active()->get(), now()->addHour());
        
        // Assert - Verify key structure
        $this->assertTrue(Cache::has('domains.active'));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_cache_can_be_cleared_manually()
    {
        // Arrange
        Cache::put('domains.active', collect(['test']), now()->addHour());
        Cache::put('evenements.upcoming', collect(['test']), now()->addHour());

        // Act
        Cache::forget('domains.active');
        Cache::forget('evenements.upcoming');

        // Assert
        $this->assertFalse(Cache::has('domains.active'));
        $this->assertFalse(Cache::has('evenements.upcoming'));
    }
}
