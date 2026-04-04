<?php

namespace App\Http\Controllers;

use App\Services\StripeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StripeWebhookController extends Controller
{
    private StripeService $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    /**
     * Gérer les webhooks Stripe.
     */
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature');

        Log::info('Webhook Stripe reçu', [
            'type' => $request->input('type'),
        ]);

        // Vérifier la signature (sauf en mode test)
        if (!$this->stripeService->verifyWebhookSignature($payload, $signature)) {
            return response()->json(['error' => 'Signature invalide'], 400);
        }

        $event = $request->all();
        $eventType = $event['type'] ?? null;

        switch ($eventType) {
            case 'checkout.session.completed':
                $this->handleCheckoutCompleted($event['data']['object']);
                break;

            case 'payment_intent.payment_failed':
                $this->handlePaymentFailed($event['data']['object']);
                break;

            case 'charge.refunded':
                $this->handleRefund($event['data']['object']);
                break;

            default:
                Log::info('Événement Stripe non géré', ['type' => $eventType]);
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Gérer un paiement réussi.
     */
    private function handleCheckoutCompleted(array $session): void
    {
        $sessionId = $session['id'];

        Log::info('Traitement paiement réussi', ['session_id' => $sessionId]);

        $order = $this->stripeService->processSuccessfulPayment($sessionId, $session);

        if ($order) {
            Log::info('Commande confirmée', ['order_id' => $order->id]);
        }
    }

    /**
     * Gérer un échec de paiement.
     */
    private function handlePaymentFailed(array $paymentIntent): void
    {
        Log::error('Paiement échoué', [
            'payment_intent_id' => $paymentIntent['id'],
            'last_error' => $paymentIntent['last_payment_error'] ?? null,
        ]);
    }

    /**
     * Gérer un remboursement.
     */
    private function handleRefund(array $charge): void
    {
        Log::info('Remboursement traité', [
            'charge_id' => $charge['id'],
            'amount_refunded' => $charge['amount_refunded'],
        ]);
    }
}
