<?php

namespace Tests\Unit\Models;

use App\Models\Presence;
use Tests\TestCase;

class PresenceTest extends TestCase
{
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
}
