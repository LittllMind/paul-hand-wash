<?php

namespace Tests\Feature;

use App\Models\Evenement;
use App\Models\Lieu;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EvenementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_evenement_has_required_fillable_attributes(): void
    {
        $lieu = Lieu::factory()->create();

        $evenement = Evenement::create([
            'titre' => 'Concert Rock',
            'description' => 'Un super concert en plein air',
            'date_debut' => '2025-06-15 19:00:00',
            'date_fin' => '2025-06-15 23:00:00',
            'lieu_id' => $lieu->id,
        ]);

        $this->assertDatabaseHas('evenements', [
            'id' => $evenement->id,
            'titre' => 'Concert Rock',
            'description' => 'Un super concert en plein air',
            'lieu_id' => $lieu->id,
        ]);

        $this->assertEquals('Concert Rock', $evenement->titre);
        $this->assertEquals('Un super concert en plein air', $evenement->description);
    }

    public function test_evenement_belongs_to_lieu(): void
    {
        $lieu = Lieu::factory()->create([
            'nom' => 'Stade de France',
            'ville' => 'Saint-Denis',
        ]);

        $evenement = Evenement::factory()->create([
            'titre' => 'Match de foot',
            'lieu_id' => $lieu->id,
        ]);

        // Test relation belongsTo
        $this->assertInstanceOf(Lieu::class, $evenement->lieu);
        $this->assertEquals('Stade de France', $evenement->lieu->nom);
        $this->assertEquals('Saint-Denis', $evenement->lieu->ville);
    }

    public function test_evenement_requires_titre(): void
    {
        $lieu = Lieu::factory()->create();

        $this->expectException(\Illuminate\Database\QueryException::class);
        
        Evenement::create([
            'description' => 'Sans titre',
            'date_debut' => '2025-06-15 19:00:00',
            'date_fin' => '2025-06-15 23:00:00',
            'lieu_id' => $lieu->id,
        ]);
    }

    public function test_evenement_requires_lieu_id(): void
    {
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        Evenement::create([
            'titre' => 'Événement sans lieu',
            'date_debut' => '2025-06-15 19:00:00',
            'date_fin' => '2025-06-15 23:00:00',
        ]);
    }

    public function test_evenement_dates_are_casted_to_datetime(): void
    {
        $lieu = Lieu::factory()->create();
        
        $evenement = Evenement::factory()->create([
            'titre' => 'Festival',
            'date_debut' => '2025-08-01 14:00:00',
            'date_fin' => '2025-08-01 23:00:00',
            'lieu_id' => $lieu->id,
        ]);

        $this->assertInstanceOf(\Carbon\Carbon::class, $evenement->date_debut);
        $this->assertInstanceOf(\Carbon\Carbon::class, $evenement->date_fin);
    }
}
