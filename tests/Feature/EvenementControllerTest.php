<?php

namespace Tests\Feature;

use App\Models\Evenement;
use App\Models\Lieu;
use App\Models\Categorie;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EvenementControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    // ==================== INDEX ====================
    public function test_index_affiche_la_liste_des_evenements(): void
    {
        $lieu = Lieu::factory()->create();
        $categorie = Categorie::factory()->create();
        
        Evenement::factory()->count(3)->create([
            'lieu_id' => $lieu->id,
            'categorie_id' => $categorie->id,
        ]);

        $response = $this->get(route('admin.evenements.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.evenements.index');
        $response->assertViewHas('evenements');
    }

    // ==================== SHOW ====================
    public function test_show_affiche_un_evenement(): void
    {
        $lieu = Lieu::factory()->create();
        $categorie = Categorie::factory()->create();
        
        $evenement = Evenement::factory()->create([
            'lieu_id' => $lieu->id,
            'categorie_id' => $categorie->id,
        ]);

        $response = $this->get(route('admin.evenements.show', $evenement));

        $response->assertStatus(200);
        $response->assertViewIs('admin.evenements.show');
        $response->assertViewHas('evenement');
    }

    // ==================== CREATE ====================
    public function test_create_affiche_le_formulaire(): void
    {
        $response = $this->get(route('admin.evenements.create'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.evenements.create');
        $response->assertViewHas('lieux');
        $response->assertViewHas('categories');
    }

    // ==================== STORE ====================
    public function test_store_cree_un_nouvel_evenement(): void
    {
        $lieu = Lieu::factory()->create();
        $categorie = Categorie::factory()->create();

        $data = [
            'titre' => 'Concert de Jazz',
            'description' => 'Un super concert',
            'date_debut' => '2026-05-01 20:00:00',
            'date_fin' => '2026-05-01 23:00:00',
            'lieu_id' => $lieu->id,
            'categorie_id' => $categorie->id,
        ];

        $response = $this->post(route('admin.evenements.store'), $data);

        $response->assertRedirect(route('admin.evenements.index'));
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('evenements', [
            'titre' => 'Concert de Jazz',
            'lieu_id' => $lieu->id,
        ]);
    }

    public function test_store_valide_les_donnees_requises(): void
    {
        $response = $this->post(route('admin.evenements.store'), []);

        $response->assertSessionHasErrors(['titre', 'lieu_id']);
    }

    // ==================== EDIT ====================
    public function test_edit_affiche_le_formulaire(): void
    {
        $lieu = Lieu::factory()->create();
        $categorie = Categorie::factory()->create();
        
        $evenement = Evenement::factory()->create([
            'lieu_id' => $lieu->id,
            'categorie_id' => $categorie->id,
        ]);

        $response = $this->get(route('admin.evenements.edit', $evenement));

        $response->assertStatus(200);
        $response->assertViewIs('admin.evenements.edit');
        $response->assertViewHas('evenement');
        $response->assertViewHas('lieux');
        $response->assertViewHas('categories');
    }

    // ==================== UPDATE ====================
    public function test_update_modifie_l_evenement(): void
    {
        $lieu = Lieu::factory()->create();
        $categorie = Categorie::factory()->create();
        
        $evenement = Evenement::factory()->create([
            'lieu_id' => $lieu->id,
            'categorie_id' => $categorie->id,
        ]);

        $data = [
            'titre' => 'Concert Rock',
            'description' => 'Description modifiée',
            'date_debut' => '2026-06-01 20:00:00',
            'date_fin' => '2026-06-01 23:00:00',
            'lieu_id' => $lieu->id,
            'categorie_id' => $categorie->id,
        ];

        $response = $this->put(route('admin.evenements.update', $evenement), $data);

        $response->assertRedirect(route('admin.evenements.index'));
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('evenements', [
            'id' => $evenement->id,
            'titre' => 'Concert Rock',
        ]);
    }

    public function test_update_valide_les_donnees_requises(): void
    {
        $lieu = Lieu::factory()->create();
        $categorie = Categorie::factory()->create();
        
        $evenement = Evenement::factory()->create([
            'lieu_id' => $lieu->id,
            'categorie_id' => $categorie->id,
        ]);

        $response = $this->put(route('admin.evenements.update', $evenement), [
            'titre' => '',
            'lieu_id' => '',
        ]);

        $response->assertSessionHasErrors(['titre', 'lieu_id']);
    }

    // ==================== DESTROY ====================
    public function test_destroy_supprime_l_evenement(): void
    {
        $lieu = Lieu::factory()->create();
        $categorie = Categorie::factory()->create();
        
        $evenement = Evenement::factory()->create([
            'lieu_id' => $lieu->id,
            'categorie_id' => $categorie->id,
        ]);

        $response = $this->delete(route('admin.evenements.destroy', $evenement));

        $response->assertRedirect(route('admin.evenements.index'));
        $response->assertSessionHas('success');
        
        $this->assertDatabaseMissing('evenements', [
            'id' => $evenement->id,
        ]);
    }
}
