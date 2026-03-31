<?php

namespace Tests\Unit\Models;

use App\Models\Lieu;
use App\Models\Presence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class PresenceTest extends TestCase
{
    use RefreshDatabase;

    public function test_presence_a_bons_attributs_fillable()
    {
        $presence = new Presence();
        $reflection = new \ReflectionClass($presence);
        $property = $reflection->getProperty('fillable');
        $property->setAccessible(true);
        $fillable = $property->getValue($presence);

        $this->assertEquals([
            'lieu_id',
            'date',
            'heure_debut',
            'heure_fin',
            'est_reserve',
        ], $fillable);
    }

    public function test_presence_a_bons_casts()
    {
        $presence = new Presence();
        $reflection = new \ReflectionClass($presence);
        $property = $reflection->getProperty('casts');
        $property->setAccessible(true);
        $casts = $property->getValue($presence);

        $this->assertEquals([
            'date' => 'date',
            'heure_debut' => 'datetime:H:i',
            'heure_fin' => 'datetime:H:i',
            'est_reserve' => 'boolean',
        ], $casts);
    }

    public function test_presence_a_relation_lieu()
    {
        $presence = new Presence();
        $this->assertTrue(
            method_exists($presence, 'lieu'),
            'Presence doit avoir une méthode lieu()'
        );
    }

    public function test_presence_structure_model_est_valide()
    {
        $this->assertInstanceOf(
            \Illuminate\Database\Eloquent\Model::class,
            new Presence()
        );

        $uses = class_uses(Presence::class);
        $this->assertContains(
            \Illuminate\Database\Eloquent\Factories\HasFactory::class,
            $uses
        );
    }

    public function test_presence_appartient_a_lieu()
    {
        $lieu = Lieu::factory()->create();
        $presence = Presence::create([
            'lieu_id' => $lieu->id,
            'date' => '2026-04-01',
            'heure_debut' => '09:00',
            'heure_fin' => '17:00',
            'est_reserve' => false,
        ]);
        $this->assertEquals($lieu->id, $presence->lieu->id);
    }

    public function test_presence_a_creneaux_horaires_valides()
    {
        $this->expectException(ValidationException::class);
        $lieu = Lieu::factory()->create();
        Presence::create([
            'lieu_id' => $lieu->id,
            'date' => '2026-04-01',
            'heure_debut' => '17:00',
            'heure_fin' => '09:00',
            'est_reserve' => false,
        ]);
    }
}
