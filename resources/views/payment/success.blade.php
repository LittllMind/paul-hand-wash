@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-lg mx-auto text-center">
        <div class="mb-6">
            <svg class="mx-auto h-20 w-20 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        
        <h1 class="text-3xl font-bold mb-4 text-gray-800">Paiement confirmé !</h1>
        
        <p class="text-gray-600 mb-6">
            Votre paiement a été reçu avec succès. Votre réservation est maintenant confirmée.
        </p>
        
        @if(isset($order))
        <div class="bg-gray-50 rounded-lg p-6 mb-6 text-left">
            <h2 class="text-lg font-semibold mb-4">Détails de la commande</h2>
            
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-600">Numéro de commande:</span>
                    <span class="font-medium">#{{ $order->id }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Montant:</span>
                    <span class="font-medium">{{ number_format($order->amount, 2, ',', ' ') }} €</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Date:</span>
                    <span class="font-medium">{{ $order->paid_at->format('d/m/Y H:i') }}</span>
                </div>
            </div>
        </div>
        @endif
        
        <div class="space-y-3">
            <a href="{{ route('home') }}" 
               class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-lg transition duration-200">
                Retour à l'accueil
            </a>
            
            @if(isset($reservation))
            <a href="{{ route('reserver.confirmation', $reservation) }}" 
               class="block text-blue-600 hover:text-blue-800 underline">
                Voir ma réservation
            </a>
            @endif
        </div>
    </div>
</div>
@endsection
