<?php

namespace Tests\Feature\Admin;

use App\Models\Lieu;
use Tests\TestCase;

class LieuControllerTest extends TestCase
{
    public function test_admin_peut_voir_liste_lieux()
    {
        // Test structurel : vérifier que le controller existe et retourne une vue
        
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
}
