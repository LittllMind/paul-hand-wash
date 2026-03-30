<?php

namespace Tests\Feature\Front;

use App\Models\Presence;
use Tests\TestCase;

class ReservationControllerTest extends TestCase
{
    // ========== T3.1: Front Liste Créneaux Disponibles ==========
    public function test_reservation_controller_existe()
    {
        $this->assertTrue(
            class_exists(\App\Http\Controllers\Front\ReservationController::class),
            'ReservationController doit exister'
        );
    }

    public function test_reservation_controller_a_methode_index()
    {
        $controller = new \App\Http\Controllers\Front\ReservationController();
        $this->assertTrue(
            method_exists($controller, 'index'),
            'ReservationController doit avoir une méthode index'
        );
    }

    public function test_reservation_controller_etend_controller_base()
    {
        $controller = new \App\Http\Controllers\Front\ReservationController();
        $this->assertInstanceOf(
            \App\Http\Controllers\Controller::class,
            $controller,
            'ReservationController doit étendre App\Http\Controllers\Controller'
        );
    }

    public function test_vue_reservations_choix_creneau_existe()
    {
        $viewPath = resource_path('views/reservations/choix-creneau.blade.php');
        $this->assertTrue(
            file_exists($viewPath),
            'La vue reservations/choix-creneau.blade.php doit exister'
        );
    }

    // ========== T3.2: Formulaire Réservation ==========
    public function test_reservation_controller_a_methode_show()
    {
        $controller = new \App\Http\Controllers\Front\ReservationController();
        $this->assertTrue(
            method_exists($controller, 'show'),
            'ReservationController doit avoir une méthode show'
        );
    }

    public function test_vue_formulaire_reservation_existe()
    {
        $viewPath = resource_path('views/reservations/formulaire.blade.php');
        $this->assertTrue(
            file_exists($viewPath),
            'La vue reservations/formulaire.blade.php doit exister'
        );
    }
}
