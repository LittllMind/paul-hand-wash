<?php

namespace Tests\Feature;

use App\Models\Categorie;
use App\Models\Lieu;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RelationLieuCategorieTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_lieu_belongs_to_many_categories(): void
    {
        $lieu = Lieu::factory()->create();
        $categorie1 = Categorie::factory()->create(['nom' => 'Restaurant']);
        $categorie2 = Categorie::factory()->create(['nom' => 'Parking']);

        $lieu->categories()->attach([$categorie1->id, $categorie2->id]);

        $this->assertCount(2, $lieu->fresh()->categories);
        $this->assertTrue($lieu->categories->contains($categorie1));
        $this->assertTrue($lieu->categories->contains($categorie2));
    }

    public function test_categorie_belongs_to_many_lieux(): void
    {
        $categorie = Categorie::factory()->create(['nom' => 'Station de lavage']);
        $lieu1 = Lieu::factory()->create(['nom' => 'Lieu A']);
        $lieu2 = Lieu::factory()->create(['nom' => 'Lieu B']);

        $categorie->lieux()->attach([$lieu1->id, $lieu2->id]);

        $this->assertCount(2, $categorie->fresh()->lieux);
        $this->assertTrue($categorie->lieux->contains($lieu1));
        $this->assertTrue($categorie->lieux->contains($lieu2));
    }

    public function test_pivot_table_has_unique_constraint(): void
    {
        $lieu = Lieu::factory()->create();
        $categorie = Categorie::factory()->create();

        $lieu->categories()->attach($categorie->id);

        $this->expectException(\Illuminate\Database\UniqueConstraintViolationException::class);
        $lieu->categories()->attach($categorie->id);
    }

    public function test_categories_are_detached_from_lieu(): void
    {
        $lieu = Lieu::factory()->create();
        $categorie = Categorie::factory()->create();

        $lieu->categories()->attach($categorie->id);
        $this->assertCount(1, $lieu->fresh()->categories);

        $lieu->categories()->detach($categorie->id);
        $this->assertCount(0, $lieu->fresh()->categories);
    }

    public function test_cascade_delete_on_lieu(): void
    {
        $lieu = Lieu::factory()->create();
        $categorie = Categorie::factory()->create();

        $lieu->categories()->attach($categorie->id);
        $this->assertDatabaseHas('categorie_lieu', ['lieu_id' => $lieu->id]);

        $lieu->delete();
        $this->assertDatabaseMissing('categorie_lieu', ['lieu_id' => $lieu->id]);
    }

    public function test_cascade_delete_on_categorie(): void
    {
        $lieu = Lieu::factory()->create();
        $categorie = Categorie::factory()->create();

        $lieu->categories()->attach($categorie->id);
        $this->assertDatabaseHas('categorie_lieu', ['categorie_id' => $categorie->id]);

        $categorie->delete();
        $this->assertDatabaseMissing('categorie_lieu', ['categorie_id' => $categorie->id]);
    }
}
