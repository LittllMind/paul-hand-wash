<?php

namespace Tests\Unit\Models;

use App\Models\Lieu;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LieuTest extends TestCase
{
    // Tests de structure sans base de données

    public function test_lieu_peut_etre_cree_avec_donnees_valides()
    {
        // Test que le modèle accepte les attributs fillable correctement
        $lieu = new Lieu([
            'nom' => 'Parking Centre Commercial',
            'adresse' => '123 Rue de Paris',
            'ville' => 'Rozier',
            'code_postal' => '12345',
            'latitude' => 45.123,
            'longitude' => 5.678,
        ]);
        
        $this->assertEquals('Parking Centre Commercial', $lieu->nom);
        $this->assertEquals('Rozier', $lieu->ville);
        $this->assertEquals(45.123, $lieu->latitude);
        $this->assertEquals(5.678, $lieu->longitude);
    }

    public function test_lieu_requiert_nom_et_adresse_dans_fillable()
    {
        // Vérifie que nom et adresse sont dans fillable (peut être assigné en masse)
        $lieu = new Lieu();
        $reflection = new \ReflectionClass($lieu);
        $property = $reflection->getProperty('fillable');
        $property->setAccessible(true);
        $fillable = $property->getValue($lieu);
        
        $this->assertContains('nom', $fillable);
        $this->assertContains('adresse', $fillable);
    }

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
