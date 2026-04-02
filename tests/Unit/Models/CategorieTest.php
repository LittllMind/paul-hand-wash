<?php

namespace Tests\Unit\Models;

use App\Models\Categorie;
use App\Models\Evenement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategorieTest extends TestCase
{
    use RefreshDatabase;

    public function test_categorie_has_fillable_attributes()
    {
        $categorie = new Categorie();
        $reflection = new \ReflectionClass($categorie);
        $property = $reflection->getProperty('fillable');
        $property->setAccessible(true);
        $fillable = $property->getValue($categorie);

        $this->assertContains('nom', $fillable);
        $this->assertContains('description', $fillable);
        $this->assertContains('couleur', $fillable);
    }

    public function test_categorie_has_many_evenements()
    {
        $categorie = Categorie::factory()->create([
            'nom' => 'Lavage Extérieur',
            'description' => 'Nettoyage extérieur complet',
            'couleur' => '#FF5733',
        ]);

        $evenement1 = Evenement::factory()->create([
            'categorie_id' => $categorie->id,
        ]);

        $evenement2 = Evenement::factory()->create([
            'categorie_id' => $categorie->id,
        ]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $categorie->evenements);
        $this->assertCount(2, $categorie->evenements);
        $this->assertTrue($categorie->evenements->contains($evenement1));
        $this->assertTrue($categorie->evenements->contains($evenement2));
    }

    public function test_categorie_peut_etre_creee_en_base()
    {
        $categorie = Categorie::create([
            'nom' => 'Lustrage',
            'description' => 'Finition brillante',
            'couleur' => '#33FF57',
        ]);

        $this->assertDatabaseHas('categories', [
            'nom' => 'Lustrage',
            'description' => 'Finition brillante',
            'couleur' => '#33FF57',
        ]);
    }
}
