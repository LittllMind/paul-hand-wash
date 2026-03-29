<?php

namespace Tests\Unit\Models;

use App\Models\Lieu;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class LieuTest extends TestCase
{
    use RefreshDatabase;

    public function test_lieu_peut_etre_cree_en_base()
    {
        $lieu = Lieu::create([
            'nom' => 'Test',
            'adresse' => '123 Rue',
            'ville' => 'Rozier',
            'code_postal' => '12345',
        ]);

        $this->assertDatabaseHas('lieux', ['nom' => 'Test']);
        $this->assertEquals('Rozier', $lieu->ville);
    }

    public function test_lieu_peut_etre_cree_avec_coordonnees()
    {
        $lieu = Lieu::create([
            'nom' => 'Parking Centre Commercial',
            'adresse' => '123 Rue de Paris',
            'ville' => 'Rozier',
            'code_postal' => '12345',
            'latitude' => 45.123,
            'longitude' => 5.678,
        ]);

        $this->assertDatabaseHas('lieux', [
            'nom' => 'Parking Centre Commercial',
            'latitude' => 45.123,
            'longitude' => 5.678,
        ]);
    }

    public function test_lieu_requiert_nom_et_adresse()
    {
        $this->expectException(\Illuminate\Database\QueryException::);

        Lieu::create([
            'ville' => 'Rozier',
            'code_postal' => '12345',
        ]);
    }

    public function test_lieu_fillable_contient_champs_necessaires()
    {
        $lieu = new Lieu();
        $reflection = new \ReflectionClass($lieu);
        $property = $reflection->getProperty('fillable');
        $property->setAccessible(true);
        $fillable = $property->getValue($lieu);

        $this->assertContains('nom', $fillable);
        $this->assertContains('adresse', $fillable);
        $this->assertContains('ville', $fillable);
        $this->assertContains('code_postal', $fillable);
        $this->assertContains('latitude', $fillable);
        $this->assertContains('longitude', $fillable);
    }

    public function test_lieu_a_relation_presences()
    {
        $lieu = new Lieu();
        $this->assertTrue(method_exists($lieu, 'presences'));
    }
}
