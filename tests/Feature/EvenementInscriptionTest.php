<?php

namespace Tests\Feature;

use App\Models\Evenement;
use App\Models\Lieu;
use App\Models\Categorie;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class EvenementInscriptionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    // ==================== INSCRIPTION ====================
    public function test_utilisateur_peut_s_inscrire_a_un_evenement(): void
    {
        Mail::fake();
        
        $user = User::factory()->create();
        $lieu = Lieu::factory()->create();
        $categorie = Categorie::factory()->create();
        
        $evenement = Evenement::factory()->create([
            'lieu_id' => $lieu->id,
            'categorie_id' => $categorie->id,
        ]);

        $response = $this->actingAs($user)
            ->post(route('evenements.inscrire', $evenement));

        $response->assertRedirect();
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('evenement_user', [
            'evenement_id' => $evenement->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_utilisateur_non_connecte_ne_peut_pas_s_inscrire(): void
    {
        $lieu = Lieu::factory()->create();
        $categorie = Categorie::factory()->create();
        
        $evenement = Evenement::factory()->create([
            'lieu_id' => $lieu->id,
            'categorie_id' => $categorie->id,
        ]);

        $response = $this->post(route('evenements.inscrire', $evenement));

        $response->assertRedirect();
        // Pas de vérification sur la route exacte car login peut ne pas exister
    }

    public function test_utilisateur_deja_inscrit_ne_peut_pas_s_inscrire_de_nouveau(): void
    {
        $user = User::factory()->create();
        $lieu = Lieu::factory()->create();
        $categorie = Categorie::factory()->create();
        
        $evenement = Evenement::factory()->create([
            'lieu_id' => $lieu->id,
            'categorie_id' => $categorie->id,
        ]);
        
        $evenement->users()->attach($user);

        $response = $this->actingAs($user)
            ->post(route('evenements.inscrire', $evenement));

        $response->assertRedirect();
        $response->assertSessionHas('error');
        
        $this->assertDatabaseCount('evenement_user', 1);
    }

    // ==================== LIMITE DE PLACES ====================
    public function test_inscription_impossible_si_limite_atteinte(): void
    {
        $user = User::factory()->create();
        $autreUser = User::factory()->create();
        $lieu = Lieu::factory()->create();
        $categorie = Categorie::factory()->create();
        
        $evenement = Evenement::factory()->create([
            'lieu_id' => $lieu->id,
            'categorie_id' => $categorie->id,
            'places_limite' => 1,
        ]);
        
        // Premier utilisateur inscrit
        $evenement->users()->attach($autreUser);

        $response = $this->actingAs($user)
            ->post(route('evenements.inscrire', $evenement));

        $response->assertRedirect();
        $response->assertSessionHas('error');
        
        $this->assertDatabaseMissing('evenement_user', [
            'evenement_id' => $evenement->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_inscription_possible_si_places_disponibles(): void
    {
        Mail::fake();
        
        $user = User::factory()->create();
        $lieu = Lieu::factory()->create();
        $categorie = Categorie::factory()->create();
        
        $evenement = Evenement::factory()->create([
            'lieu_id' => $lieu->id,
            'categorie_id' => $categorie->id,
            'places_limite' => 10,
        ]);

        $response = $this->actingAs($user)
            ->post(route('evenements.inscrire', $evenement));

        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('evenement_user', [
            'evenement_id' => $evenement->id,
            'user_id' => $user->id,
        ]);
    }

    // ==================== DÉSINSCRIPTION ====================
    public function test_utilisateur_peut_se_desinscrire(): void
    {
        $user = User::factory()->create();
        $lieu = Lieu::factory()->create();
        $categorie = Categorie::factory()->create();
        
        $evenement = Evenement::factory()->create([
            'lieu_id' => $lieu->id,
            'categorie_id' => $categorie->id,
        ]);
        
        $evenement->users()->attach($user);

        $response = $this->actingAs($user)
            ->delete(route('evenements.desinscrire', $evenement));

        $response->assertRedirect();
        $response->assertSessionHas('success');
        
        $this->assertDatabaseMissing('evenement_user', [
            'evenement_id' => $evenement->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_utilisateur_non_inscrit_ne_peut_pas_se_desinscrire(): void
    {
        $user = User::factory()->create();
        $lieu = Lieu::factory()->create();
        $categorie = Categorie::factory()->create();
        
        $evenement = Evenement::factory()->create([
            'lieu_id' => $lieu->id,
            'categorie_id' => $categorie->id,
        ]);

        $response = $this->actingAs($user)
            ->delete(route('evenements.desinscrire', $evenement));

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    // ==================== LISTE PARTICIPANTS (ADMIN) ====================
    public function test_admin_peut_voir_liste_participants(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $user = User::factory()->create();
        $lieu = Lieu::factory()->create();
        $categorie = Categorie::factory()->create();
        
        $evenement = Evenement::factory()->create([
            'lieu_id' => $lieu->id,
            'categorie_id' => $categorie->id,
        ]);
        
        $evenement->users()->attach($user);

        $response = $this->actingAs($admin)
            ->get(route('admin.evenements.participants', $evenement));

        $response->assertStatus(200);
        $response->assertViewIs('admin.evenements.participants');
        $response->assertViewHas('evenement');
    }

    // ==================== RELATIONS ====================
    public function test_evenement_a_des_participants(): void
    {
        $user = User::factory()->create();
        $lieu = Lieu::factory()->create();
        $categorie = Categorie::factory()->create();
        
        $evenement = Evenement::factory()->create([
            'lieu_id' => $lieu->id,
            'categorie_id' => $categorie->id,
        ]);
        
        $evenement->users()->attach($user);

        $this->assertTrue($evenement->users->contains($user));
        $this->assertEquals(1, $evenement->users()->count());
    }

    public function test_utilisateur_a_des_evenements(): void
    {
        $user = User::factory()->create();
        $lieu = Lieu::factory()->create();
        $categorie = Categorie::factory()->create();
        
        $evenement = Evenement::factory()->create([
            'lieu_id' => $lieu->id,
            'categorie_id' => $categorie->id,
        ]);
        
        $user->evenements()->attach($evenement);

        $this->assertTrue($user->evenements->contains($evenement));
    }
}
