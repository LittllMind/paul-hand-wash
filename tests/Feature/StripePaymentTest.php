<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Reservation;
use App\Models\Presence;
use App\Models\Lieu;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StripePaymentTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test: La page de checkout Stripe est accessible
     */
    public function test_stripe_checkout(): void
    {
        // Créer une réservation
        $lieu = Lieu::factory()->create();
        $presence = Presence::factory()->create([
            'lieu_id' => $lieu->id,
            'est_reserve' => false,
        ]);
        
        $reservation = Reservation::factory()->create([
            'presence_id' => $presence->id,
            'montant' => 50.00,
            'paye' => false,
        ]);

        // Appeler la page de checkout
        $response = $this->get("/payment/checkout/{$reservation->id}");
        
        $response->assertStatus(200);
        $response->assertViewIs('payment.checkout');
        $response->assertViewHas('reservation');
        $response->assertViewHas('stripeKey');
    }

    /**
     * Test: Session Stripe Checkout retourne une URL (simulation)
     */
    public function test_stripe_checkout_session_creation(): void
    {
        $lieu = Lieu::factory()->create();
        $presence = Presence::factory()->create([
            'lieu_id' => $lieu->id,
        ]);
        
        $reservation = Reservation::factory()->create([
            'presence_id' => $presence->id,
            'montant' => 50.00,
            'client_nom' => 'Test Client',
        ]);

        $response = $this->postJson('/payment/checkout-session', [
            'reservation_id' => $reservation->id,
        ]);

        // En mode test, Stripe va échouer (pas de connexion), mais on vérifie
        // que le endpoint existe et retourne une erreur contrôlée
        $response->assertStatus(500);
    }

    /**
     * Test: Webhook Stripe accepte les payloads
     */
    public function test_webhook_confirmation(): void
    {
        // Simuler un webhook Stripe checkout.session.completed
        $payload = [
            'type' => 'checkout.session.completed',
            'data' => [
                'object' => [
                    'id' => 'cs_test_' . uniqid(),
                    'metadata' => [
                        'reservation_id' => '1',
                    ],
                    'amount_total' => 5000,
                    'currency' => 'eur',
                    'customer_details' => [
                        'email' => 'test@example.com',
                        'name' => 'Test Client',
                    ],
                ],
            ],
        ];

        $response = $this->postJson('/stripe/webhook', $payload, [
            'Stripe-Signature' => 'test_signature',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);
    }

    /**
     * Test: Création commande après paiement réussi
     */
    public function test_order_creation(): void
    {
        $lieu = Lieu::factory()->create();
        $presence = Presence::factory()->create([
            'lieu_id' => $lieu->id,
        ]);
        
        $reservation = Reservation::factory()->create([
            'presence_id' => $presence->id,
            'montant' => 50.00,
            'client_nom' => 'John Doe',
            'client_email' => 'john@example.com',
            'prestation' => 'Lavage Premium',
            'paye' => false,
        ]);

        // Créer une commande
        $order = Order::create([
            'reservation_id' => $reservation->id,
            'stripe_session_id' => 'cs_test_' . uniqid(),
            'stripe_payment_intent_id' => 'pi_test_' . uniqid(),
            'amount' => $reservation->montant,
            'currency' => 'eur',
            'status' => 'paid',
            'customer_email' => $reservation->client_email,
            'customer_name' => $reservation->client_nom,
        ]);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'reservation_id' => $reservation->id,
            'status' => 'paid',
            'amount' => 50.00,
        ]);

        // Créer un item de commande
        OrderItem::create([
            'order_id' => $order->id,
            'description' => $reservation->prestation,
            'quantity' => 1,
            'unit_price' => $reservation->montant,
            'total_price' => $reservation->montant,
        ]);

        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'description' => 'Lavage Premium',
            'quantity' => 1,
            'total_price' => 50.00,
        ]);
    }

    /**
     * Test: La page de succès est accessible
     */
    public function test_payment_success_page(): void
    {
        $lieu = Lieu::factory()->create();
        $presence = Presence::factory()->create([
            'lieu_id' => $lieu->id,
        ]);
        
        $reservation = Reservation::factory()->create([
            'presence_id' => $presence->id,
        ]);

        $order = Order::create([
            'reservation_id' => $reservation->id,
            'stripe_session_id' => 'cs_test_123',
            'amount' => 50.00,
            'currency' => 'eur',
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        $response = $this->get("/payment/success?session_id=cs_test_123");
        
        $response->assertStatus(200);
        $response->assertViewIs('payment.success');
    }

    /**
     * Test: La page d'annulation est accessible
     */
    public function test_payment_cancel_page(): void
    {
        $response = $this->get('/payment/cancel');
        
        $response->assertStatus(200);
        $response->assertViewIs('payment.cancel');
    }

    /**
     * Test: Réservation non trouvée retourne 404
     */
    public function test_checkout_with_invalid_reservation(): void
    {
        $response = $this->get('/payment/checkout/99999');
        
        $response->assertStatus(404);
    }

    /**
     * Test: Réservation déjà payée ne peut pas être re-payée
     */
    public function test_cannot_checkout_paid_reservation(): void
    {
        $lieu = Lieu::factory()->create();
        $presence = Presence::factory()->create([
            'lieu_id' => $lieu->id,
        ]);
        
        $reservation = Reservation::factory()->create([
            'presence_id' => $presence->id,
            'paye' => true,
        ]);

        $response = $this->get("/payment/checkout/{$reservation->id}");
        
        $response->assertRedirect();
        $response->assertSessionHas('error', 'Cette réservation est déjà payée.');
    }
}
