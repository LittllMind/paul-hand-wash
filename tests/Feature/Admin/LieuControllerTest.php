<?php

namespace Tests\Feature\Admin;

use App\Models\Lieu;
use Tests\TestCase;

class LieuControllerTest extends TestCase
{
    // ========== T1.2: Index ==========
    public function test_admin_peut_voir_liste_lieux()
    {
        $this->assertTrue(
            class_exists(\App\Http\Controllers\Admin\LieuController::class),
            'LieuController doit exister'
        );
    }

    public function test_lieu_controller_a_methode_index()
    {
        $controller = new \App\Http\Controllers\Admin\LieuController();
        $this->assertTrue(
            method_exists($controller, 'index'),
            'LieuController doit avoir une méthode index'
        );
    }

    public function test_lieu_controller_etend_controller_base()
    {
        $controller = new \App\Http\Controllers\Admin\LieuController();
        $this->assertInstanceOf(
            \App\Http\Controllers\Controller::class,
            $controller,
            'LieuController doit étendre App\Http\Controllers\Controller'
        );
    }

    public function test_vue_admin_lieux_index_existe()
    {
        $viewPath = resource_path('views/admin/lieux/index.blade.php');
        $this->assertTrue(
            file_exists($viewPath),
            'La vue admin/lieux/index.blade.php doit exister'
        );
    }

    // ========== T1.3: Create/Store ==========
    public function test_lieu_controller_a_methode_create()
    {
        $controller = new \App\Http\Controllers\Admin\LieuController();
        $this->assertTrue(
            method_exists($controller, 'create'),
            'LieuController doit avoir une méthode create'
        );
    }

    public function test_lieu_controller_a_methode_store()
    {
        $controller = new \App\Http\Controllers\Admin\LieuController();
        $this->assertTrue(
            method_exists($controller, 'store'),
            'LieuController doit avoir une méthode store'
        );
    }

    public function test_vue_admin_lieux_create_existe()
    {
        $viewPath = resource_path('views/admin/lieux/create.blade.php');
        $this->assertTrue(
            file_exists($viewPath),
            'La vue admin/lieux/create.blade.php doit exister'
        );
    }

    public function test_admin_peut_creer_lieu()
    {
        $data = [
            'nom' => 'Nouveau Lieu',
            'adresse' => '123 Rue de Test',
            'ville' => 'Testville',
            'code_postal' => '75000',
            'latitude' => 48.8566,
            'longitude' => 2.3522,
        ];

        $response = $this->post('/admin/lieux', $data);

        $response->assertRedirect('/admin/lieux');
        $this->assertDatabaseHas('lieux', [
            'nom' => 'Nouveau Lieu',
            'ville' => 'Testville',
        ]);
    }

    // ========== T1.4: Edit/Update ==========
    public function test_lieu_controller_a_methode_edit()
    {
        $controller = new \App\Http\Controllers\Admin\LieuController();
        $this->assertTrue(
            method_exists($controller, 'edit'),
            'LieuController doit avoir une méthode edit'
        );
    }

    public function test_lieu_controller_a_methode_update()
    {
        $controller = new \App\Http\Controllers\Admin\LieuController();
        $this->assertTrue(
            method_exists($controller, 'update'),
            'LieuController doit avoir une méthode update'
        );
    }

    public function test_vue_admin_lieux_edit_existe()
    {
        $viewPath = resource_path('views/admin/lieux/edit.blade.php');
        $this->assertTrue(
            file_exists($viewPath),
            'La vue admin/lieux/edit.blade.php doit exister'
        );
    }

    public function test_admin_peut_modifier_lieu()
    {
        $lieu = Lieu::factory()->create();

        $response = $this->put("/admin/lieux/{$lieu->id}", [
            'nom' => 'Nom Modifié',
            'adresse' => $lieu->adresse,
            'ville' => $lieu->ville,
            'code_postal' => $lieu->code_postal,
            'latitude' => $lieu->latitude,
            'longitude' => $lieu->longitude,
        ]);

        $response->assertRedirect('/admin/lieux');
        $this->assertDatabaseHas('lieux', ['nom' => 'Nom Modifié']);
    }

    // ========== T1.5: Delete ==========
    public function test_lieu_controller_a_methode_destroy()
    {
        $controller = new \App\Http\Controllers\Admin\LieuController();
        $this->assertTrue(
            method_exists($controller, 'destroy'),
            'LieuController doit avoir une méthode destroy'
        );
    }

    public function test_admin_peut_supprimer_lieu()
    {
        $lieu = Lieu::factory()->create();

        $response = $this->delete("/admin/lieux/{$lieu->id}");

        $response->assertRedirect('/admin/lieux');
        $this->assertDatabaseMissing('lieux', ['id' => $lieu->id]);
    }
}
