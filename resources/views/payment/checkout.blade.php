@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-lg mx-auto">
        <h1 class="text-3xl font-bold mb-6">Paiement de votre réservation</h1>
        
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Récapitulatif</h2>
            
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Client:</span>
                    <span class="font-medium">{{ $reservation->client_nom }}</span>
                </div>
                
                <div class="flex justify-between">
                    <span class="text-gray-600">Prestation:</span>
                    <span class="font-medium">{{ $reservation->prestation }}</span>
                </div>
                
                @if($reservation->presence && $reservation->presence->lieu)
                <div class="flex justify-between">
                    <span class="text-gray-600">Lieu:</span>
                    <span class="font-medium">{{ $reservation->presence->lieu->nom }}</span>
                </div>
                @endif
                
                <div class="border-t pt-3 flex justify-between text-lg font-bold">
                    <span>Total:</span>
                    <span>{{ number_format($reservation->montant, 2, ',', ' ') }} €</span>
                </div>
            </div>
        </div>

        <button id="checkout-button" 
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 px-6 rounded-lg transition duration-200">
            Payer avec Stripe
        </button>
        
        <p class="text-center text-sm text-gray-500 mt-4">
            Paiement sécurisé par Stripe. Vous serez redirigé vers Stripe pour finaliser le paiement.
        </p>
    </div>
</div>

<script src="https://js.stripe.com/v3/"></script>
<script>
    const stripe = Stripe('{{ $stripeKey }}');
    
    document.getElementById('checkout-button').addEventListener('click', async function() {
        const button = this;
        button.disabled = true;
        button.innerHTML = 'Chargement...';
        
        try {
            const response = await fetch('/payment/checkout-session', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    reservation_id: '{{ $reservation->id }}'
                })
            });
            
            const data = await response.json();
            
            if (data.error) {
                alert(data.error);
                button.disabled = false;
                button.innerHTML = 'Payer avec Stripe';
            } else {
                // Redirection vers Stripe Checkout
                window.location.href = data.url;
            }
        } catch (error) {
            console.error('Erreur:', error);
            alert('Une erreur est survenue. Veuillez réessayer.');
            button.disabled = false;
            button.innerHTML = 'Payer avec Stripe';
        }
    });
</script>
@endsection
