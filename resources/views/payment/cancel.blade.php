@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-lg mx-auto text-center">
        <div class="mb-6">
            <svg class="mx-auto h-20 w-20 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </div>
        
        <h1 class="text-3xl font-bold mb-4 text-gray-800">Paiement annulé</h1>
        
        <p class="text-gray-600 mb-6">
            Le paiement a été annulé ou n'a pas pu être traité. Votre réservation n'a pas été confirmée.
        </p>
        
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6 text-left">
            <p class="text-yellow-800 text-sm">
                <strong>Note:</strong> Vous pouvez réessayer le paiement à tout moment depuis votre espace réservation.
            </p>
        </div>
        
        <div class="space-y-3">
            <a href="{{ route('home') }}" 
               class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-lg transition duration-200">
                Retour à l'accueil
            </a>
            
            <a href="{{ route('reserver') }}" 
               class="block text-blue-600 hover:text-blue-800 underline">
                Effectuer une nouvelle réservation
            </a>
        </div>
    </div>
</div>
@endsection
