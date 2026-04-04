<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Domain;

class DomainControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_peut_voir_liste_domaines(): void
    {
        Domain::create([
            'slug' => 'test',
            'name' => 'Test',
            'description' => 'Test',
            'image' => 'test.jpg',
            'active' => true,
        ]);

        $response = $this->get('/admin/domaines');

        $response->assertStatus(200);
        $response->assertViewIs('admin.domaines.index');
        $response->assertSee('Test');
    }

    public function test_admin_peut_creer_domaine(): void
    {
        $response = $this->post('/admin/domaines', [
            'slug' => 'nouveau-domaine',
            'name' => 'Nouveau Domaine',
            'description' => 'Description test',
            'image' => 'images/test.jpg',
            'active' => true,
        ]);

        $response->assertRedirect('/admin/domaines');
        $this->assertDatabaseHas('domains', [
            'slug' => 'nouveau-domaine',
            'name' => 'Nouveau Domaine',
        ]);
    }

    public function test_admin_peut_modifier_domaine(): void
    {
        $domaine = Domain::create([
            'slug' => 'ancien',
            'name' => 'Ancien Nom',
            'description' => 'Test',
            'image' => 'test.jpg',
            'active' => true,
        ]);

        $response = $this->put("/admin/domaines/{$domaine->id}", [
            'slug' => 'nouveau',
            'name' => 'Nouveau Nom',
            'description' => 'Nouvelle description',
            'image' => 'images/nouveau.jpg',
            'active' => false,
        ]);

        $response->assertRedirect('/admin/domaines');
        $this->assertDatabaseHas('domains', [
            'id' => $domaine->id,
            'slug' => 'nouveau',
            'name' => 'Nouveau Nom',
            'active' => false,
        ]);
    }

    public function test_admin_peut_supprimer_domaine(): void
    {
        $domaine = Domain::create([
            'slug' => 'a-supprimer',
            'name' => 'À Supprimer',
            'description' => 'Test',
            'image' => 'test.jpg',
            'active' => true,
        ]);

        $response = $this->delete("/admin/domaines/{$domaine->id}");

        $response->assertRedirect('/admin/domaines');
        $this->assertDatabaseMissing('domains', [
            'id' => $domaine->id,
        ]);
    }

    public function test_slug_doit_etre_unique(): void
    {
        Domain::create([
            'slug' => 'existant',
            'name' => 'Existant',
            'description' => 'Test',
            'image' => 'test.jpg',
            'active' => true,
        ]);

        $response = $this->post('/admin/domaines', [
            'slug' => 'existant',
            'name' => 'Doublon',
            'description' => 'Test',
            'image' => 'test.jpg',
            'active' => true,
        ]);

        $response->assertSessionHasErrors('slug');
    }
}
