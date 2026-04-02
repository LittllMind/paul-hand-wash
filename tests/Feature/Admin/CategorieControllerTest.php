<?php

namespace Tests\Feature\Admin;

use App\Models\Categorie;
use Tests\TestCase;

class CategorieControllerTest extends TestCase
{
    // ========== T2.2: Index ==========
    public function test_admin_peut_voir_liste_categories()
    {
        $this->assertTrue(
            class_exists(\App\Http\Controllers\Admin\CategorieController::class),
            'CategorieController doit exister'
        );
    }

    public function test_categorie_controller_a_methode_index()
    {
        $controller = new \App\Http\Controllers\Admin\CategorieController();
        $this->assertTrue(
            method_exists($controller, 'index'),
            'CategorieController doit avoir une méthode index'
        );
    }

    public function test_categorie_controller_etend_controller_base()
    {
        $controller = new \App\Http\Controllers\Admin\CategorieController();
        $this->assertInstanceOf(
            \App\Http\Controllers\Controller::class,
            $controller,
            'CategorieController doit étendre App\Http\Controllers\Controller'
        );
    }

    public function test_vue_admin_categories_index_existe()
    {
        $viewPath = resource_path('views/admin/categories/index.blade.php');
        $this->assertTrue(
            file_exists($viewPath),
            'La vue admin/categories/index.blade.php doit exister'
        );
    }

    // ========== T2.2: Create/Store ==========
    public function test_categorie_controller_a_methode_create()
    {
        $controller = new \App\Http\Controllers\Admin\CategorieController();
        $this->assertTrue(
            method_exists($controller, 'create'),
            'CategorieController doit avoir une méthode create'
        );
    }

    public function test_categorie_controller_a_methode_store()
    {
        $controller = new \App\Http\Controllers\Admin\CategorieController();
        $this->assertTrue(
            method_exists($controller, 'store'),
            'CategorieController doit avoir une méthode store'
        );
    }

    public function test_vue_admin_categories_create_existe()
    {
        $viewPath = resource_path('views/admin/categories/create.blade.php');
        $this->assertTrue(
            file_exists($viewPath),
            'La vue admin/categories/create.blade.php doit exister'
        );
    }

    public function test_admin_peut_creer_categorie()
    {
        $data = [
            'nom' => 'Nouvelle Catégorie',
            'description' => 'Description de test',
            'couleur' => '#FF0000',
        ];

        $response = $this->post('/admin/categories', $data);

        $response->assertRedirect('/admin/categories');
        $this->assertDatabaseHas('categories', [
            'nom' => 'Nouvelle Catégorie',
            'description' => 'Description de test',
        ]);
    }

    // ========== T2.2: Edit/Update ==========
    public function test_categorie_controller_a_methode_edit()
    {
        $controller = new \App\Http\Controllers\Admin\CategorieController();
        $this->assertTrue(
            method_exists($controller, 'edit'),
            'CategorieController doit avoir une méthode edit'
        );
    }

    public function test_categorie_controller_a_methode_update()
    {
        $controller = new \App\Http\Controllers\Admin\CategorieController();
        $this->assertTrue(
            method_exists($controller, 'update'),
            'CategorieController doit avoir une méthode update'
        );
    }

    public function test_vue_admin_categories_edit_existe()
    {
        $viewPath = resource_path('views/admin/categories/edit.blade.php');
        $this->assertTrue(
            file_exists($viewPath),
            'La vue admin/categories/edit.blade.php doit exister'
        );
    }

    public function test_admin_peut_modifier_categorie()
    {
        $categorie = Categorie::factory()->create([
            'nom' => 'Ancien Nom',
            'description' => 'Ancienne description',
            'couleur' => '#000000',
        ]);

        $response = $this->put("/admin/categories/{$categorie->id}", [
            'nom' => 'Nom Modifié',
            'description' => 'Nouvelle description',
            'couleur' => '#FFFFFF',
        ]);

        $response->assertRedirect('/admin/categories');
        $this->assertDatabaseHas('categories', ['nom' => 'Nom Modifié']);
    }

    // ========== T2.2: Delete ==========
    public function test_categorie_controller_a_methode_destroy()
    {
        $controller = new \App\Http\Controllers\Admin\CategorieController();
        $this->assertTrue(
            method_exists($controller, 'destroy'),
            'CategorieController doit avoir une méthode destroy'
        );
    }

    public function test_admin_peut_supprimer_categorie()
    {
        $categorie = Categorie::factory()->create();

        $response = $this->delete("/admin/categories/{$categorie->id}");

        $response->assertRedirect('/admin/categories');
        $this->assertDatabaseMissing('categories', ['id' => $categorie->id]);
    }
}
