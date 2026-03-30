<?php

namespace Tests\Unit\Models;

use App\Models\Lieu;
use Tests\TestCase;

class LieuTest extends TestCase
{
    // Tests sans RefreshDatabase car SQLite non disponible
    // Ces tests valident la structure du modèle

    public function test_lieu_a_bons_attributs_fillable()
    {
        $lieu = new Lieu();
        $reflection = new \ReflectionClass($lieu);
        $property = $reflection->getProperty('fillable');
        $property->setAccessible(true);
        $fillable = $property->getValue($lieu);

        $this->assertEquals([
            'nom',
            'adresse',
            'ville',
            'code_postal',
            'latitude',
            'longitude',
        ], $fillable);
    }

    public function test_lieu_a_bons_casts()
    {
        $lieu = new Lieu();
        $reflection = new \ReflectionClass($lieu);
        $property = $reflection->getProperty('casts');
        $property->setAccessible(true);
        $casts = $property->getValue($lieu);

        $this->assertEquals([
            'latitude' => 'float',
            'longitude' => 'float',
        ], $casts);
    }

    public function test_lieu_a_relation_presences()
    {
        $lieu = new Lieu();
        $this->assertTrue(method_exists($lieu, 'presences'));
    }

    public function test_lieu_structure_model_est_valide()
    {
        // Vérification que la classe Lieu existe et étend Model
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Model::class, new Lieu());
        
        // Vérification qu'elle utilise HasFactory
        $uses = class_uses(Lieu::class);
        $this->assertContains(\Illuminate\Database\Eloquent\Factories\HasFactory::class, $uses);
    }
}
