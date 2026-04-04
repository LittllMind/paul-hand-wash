<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Order;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    private StripeService $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    /**
     * Afficher la page de checkout.
     */
    public function checkout(string $reservationId)
    {
        $reservation = Reservation::with('presence.lieu')->findOrFail($reservationId);

        // Vérifier si déjà payée
        if ($reservation->paye) {
            return redirect()->route('reserver.confirmation', $reservation)
                ->with('error', 'Cette réservation est déjà payée.');
        }

        return view('payment.checkout', [
            'reservation' => $reservation,
            'stripeKey' => config('services.stripe.key'),
        ]);
    }

    /**
     * Créer une session de checkout Stripe.
     */
    public function createCheckoutSession(Request $request)
    {
        $request->validate([
            'reservation_id' => 'required|exists:reservations,id',
        ]);

        $reservation = Reservation::with('presence.lieu')
            ->findOrFail($request->input('reservation_id'));

        // Vérifier si déjà payée
        if ($reservation->paye) {
            return response()->json([
                'error' => 'Cette réservation est déjà payée.',
            ], 400);
        }

        try {
            $session = $this->stripeService->createCheckoutSession($reservation);

            return response()->json([
                'id' => $session->id,
                'url' => $session->url,
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur création session Stripe: ' . $e->getMessage());
            
            return response()->json([
                'error' => 'Erreur lors de la création de la session de paiement.',
            ], 500);
        }
    }

    /**
     * Page de succès après paiement.
     */
    public function success(Request $request)
    {
        $sessionId = $request->query('session_id');

        if (!$sessionId) {
            return redirect()->route('home')->with('error', 'Session de paiement invalide.');
        }

        $order = Order::with('reservation', 'items')
            ->where('stripe_session_id', $sessionId)
            ->first();

        if (!$order) {
            return redirect()->route('home')->with('error', 'Commande non trouvée.');
        }

        return view('payment.success', [
            'order' => $order,
            'reservation' => $order->reservation,
        ]);
    }

    /**
     * Page d'annulation de paiement.
     */
    public function cancel()
    {
        return view('payment.cancel');
    }
}
