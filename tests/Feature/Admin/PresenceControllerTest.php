<?php

namespace Tests\Feature\Admin;

use App\Models\Presence;
use Tests\TestCase;

class PresenceControllerTest extends TestCase
{
    // ========== T2.2: Calendrier Vue Mensuelle ==========
    public function test_presence_controller_existe()
    {
        $this->assertTrue(
            class_exists(\App\Http\Controllers\Admin\PresenceController::class),
            'PresenceController doit exister'
        );
    }

    public function test_presence_controller_a_methode_index()
    {
        $controller = new \App\Http\Controllers\Admin\PresenceController();
        $this->assertTrue(
            method_exists($controller, 'index'),
            'PresenceController doit avoir une méthode index'
        );
    }

    public function test_presence_controller_etend_controller_base()
    {
        $controller = new \App\Http\Controllers\Admin\PresenceController();
        $this->assertInstanceOf(
            \App\Http\Controllers\Controller::class,
            $controller,
            'PresenceController doit étendre App\Http\Controllers\Controller'
        );
    }

    public function test_vue_admin_presences_calendrier_existe()
    {
        $viewPath = resource_path('views/admin/presences/calendrier.blade.php');
        $this->assertTrue(
            file_exists($viewPath),
            'La vue admin/presences/calendrier.blade.php doit exister'
        );
    }

    // ========== T2.3: Création Créneaux Batch ==========
    public function test_presence_controller_a_methode_create_batch()
    {
        $controller = new \App\Http\Controllers\Admin\PresenceController();
        $this->assertTrue(
            method_exists($controller, 'createBatch'),
            'PresenceController doit avoir une méthode createBatch'
        );
    }

    public function test_presence_controller_a_methode_store_batch()
    {
        $controller = new \App\Http\Controllers\Admin\PresenceController();
        $this->assertTrue(
            method_exists($controller, 'storeBatch'),
            'PresenceController doit avoir une méthode storeBatch'
        );
    }

    public function test_vue_admin_presences_batch_create_existe()
    {
        $viewPath = resource_path('views/admin/presences/batch-create.blade.php');
        $this->assertTrue(
            file_exists($viewPath),
            'La vue admin/presences/batch-create.blade.php doit exister'
        );
    }
}
