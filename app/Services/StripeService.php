<?php

namespace App\Services;

use App\Models\Reservation;
use App\Models\Order;
use App\Models\OrderItem;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Illuminate\Support\Facades\Log;

class StripeService
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * Créer une session de checkout Stripe.
     */
    public function createCheckoutSession(Reservation $reservation): Session
    {
        $lieu = $reservation->presence->lieu ?? null;
        
        $lineItems = [
            [
                'price_data' => [
                    'currency' => config('services.stripe.currency', 'eur'),
                    'product_data' => [
                        'name' => $reservation->prestation ?: 'Réservation Lavage',
                        'description' => $lieu ? "Lieu: {$lieu->nom}" : 'Paul Hand Wash',
                    ],
                    'unit_amount' => (int) ($reservation->montant * 100), // en centimes
                ],
                'quantity' => 1,
            ],
        ];

        $sessionData = [
            'payment_method_types' => ['card'],
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => route('payment.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('payment.cancel'),
            'metadata' => [
                'reservation_id' => $reservation->id,
            ],
            'customer_email' => $reservation->client_email ?: null,
        ];

        $session = Session::create($sessionData);

        // Créer la commande en attente
        Order::create([
            'reservation_id' => $reservation->id,
            'stripe_session_id' => $session->id,
            'amount' => $reservation->montant,
            'currency' => config('services.stripe.currency', 'eur'),
            'status' => Order::STATUS_PENDING,
            'customer_email' => $reservation->client_email,
            'customer_name' => $reservation->client_nom,
        ]);

        Log::info('Session Stripe créée', [
            'session_id' => $session->id,
            'reservation_id' => $reservation->id,
        ]);

        return $session;
    }

    /**
     * Récupérer une session Stripe.
     */
    public function getSession(string $sessionId): Session
    {
        return Session::retrieve($sessionId);
    }

    /**
     * Vérifier la signature du webhook Stripe.
     */
    public function verifyWebhookSignature(string $payload, string $signature): bool
    {
        $secret = config('services.stripe.webhook_secret');
        
        if (empty($secret) || $secret === 'whsec_test') {
            // Mode test - accepter sans vérification
            return true;
        }

        try {
            \Stripe\Webhook::constructEvent($payload, $signature, $secret);
            return true;
        } catch (\Exception $e) {
            Log::error('Webhook Stripe signature invalide: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Traiter un paiement réussi.
     */
    public function processSuccessfulPayment(string $sessionId, array $sessionData): ?Order
    {
        $order = Order::where('stripe_session_id', $sessionId)->first();

        if (!$order) {
            Log::error('Commande non trouvée pour la session', ['session_id' => $sessionId]);
            return null;
        }

        if ($order->isPaid()) {
            Log::info('Commande déjà payée', ['order_id' => $order->id]);
            return $order;
        }

        // Mettre à jour la commande
        $order->update([
            'stripe_payment_intent_id' => $sessionData['payment_intent'] ?? null,
            'status' => Order::STATUS_PAID,
            'paid_at' => now(),
            'customer_email' => $sessionData['customer_details']['email'] ?? $order->customer_email,
            'customer_name' => $sessionData['customer_details']['name'] ?? $order->customer_name,
            'stripe_response' => $sessionData,
        ]);

        // Créer l'item de commande
        OrderItem::create([
            'order_id' => $order->id,
            'description' => $order->reservation->prestation ?: 'Prestation',
            'quantity' => 1,
            'unit_price' => $order->amount,
            'total_price' => $order->amount,
        ]);

        // Marquer la réservation comme payée
        $order->reservation->update(['paye' => true]);

        Log::info('Paiement traité avec succès', [
            'order_id' => $order->id,
            'reservation_id' => $order->reservation_id,
        ]);

        return $order;
    }
}
