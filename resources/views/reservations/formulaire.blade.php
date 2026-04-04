@extends('layouts.front')

@section('meta')
    <title>Finaliser votre réservation - Paolo Wash | {{ $creneau->date->format('d/m/Y') }} {{ $creneau->lieu->nom }}</title>
    <meta name="description" content="Finalisez votre réservation de lavage auto à {{ $creneau->lieu->nom }} le {{ $creneau->date->translatedFormat('d F Y') }}.">
    <meta name="keywords" content="réservation, lavage auto, {{ $creneau->lieu->nom }}, Paolo Wash">
    <link rel="canonical" href="{{ route('reserver.show', $creneau) }}">
    
    <meta property="og:title" content="Finaliser votre réservation - Paolo Wash">
    <meta property="og:description" content="Réservation pour le {{ $creneau->date->format('d/m/Y') }} à {{ $creneau->lieu->nom }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ route('reserver.show', $creneau) }}">
    <meta property="og:site_name" content="Paolo Wash">
    
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="Finaliser votre réservation - Paolo Wash">
@endsection

@push('styles')
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; }
        h1 { color: #333; }
        .recap { background: #e3f2fd; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], input[type="email"], input[type="tel"] {
            width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;
        }
        .prestation-option { border: 2px solid #ddd; padding: 15px; margin: 10px 0; border-radius: 8px; cursor: pointer; }
        .prestation-option:hover { border-color: #2196F3; }
        .prestation-option input[type="radio"] { margin-right: 10px; }
        .prestation-name { font-weight: bold; font-size: 1.1em; }
        .prestation-price { color: #4CAF50; font-weight: bold; }
        .btn-submit { background: #4CAF50; color: white; padding: 15px 30px; border: none; border-radius: 4px; font-size: 1.1em; cursor: pointer; width: 100%; }
        .btn-submit:hover { background: #45a049; }
        .back-link { display: block; margin-top: 20px; text-align: center; color: #666; }
    </style>
@endpush

@section('content')
    <h1>🚗 Finaliser votre réservation</h1>

    <div class="recap">
        <h3>📍 Créneau sélectionné</h3>
        <p>
            <strong>{{ $creneau->lieu->nom }}</strong><br>
            📅 {{ $creneau->date->translatedFormat('l d F Y') }}<br>
            🕐 {{ $creneau->heure_debut->format('H:i') }} - {{ $creneau->heure_fin->format('H:i') }}
        </p>
    </div>

    <form action="{{ route('reserver.store', $creneau) }}" method="POST">
        @csrf
        
        <div class="form-group">
            <label for="client_nom">Votre nom *</label>
            <input type="text" name="client_nom" id="client_nom" required placeholder="Jean Dupont">
        </div>

        <div class="form-group">
            <label for="client_telephone">Téléphone *</label>
            <input type="tel" name="client_telephone" id="client_telephone" required placeholder="06 12 34 56 78">
        </div>

        <div class="form-group">
            <label for="client_email">Email *</label>
            <input type="email" name="client_email" id="client_email" required placeholder="jean@example.com">
        </div>

        <div class="form-group">
            <label>Choisissez votre prestation *</label>
            
            @foreach($prestations as $key => $prestation)
                <label class="prestation-option" style="display: block;">
                    <input type="radio" name="prestation" value="{{ $key }}" {{ $loop->first ? 'checked' : '' }} required>
                    <span class="prestation-name">{{ $prestation['nom'] }}</span> 
                    <span class="prestation-price">{{ $prestation['prix'] }}€</span>
                    <br>
                    <small>⏱️ {{ $prestation['duree'] }} - {{ $prestation['description'] }}</small>
                </label>
            @endforeach
        </div>

        <button type="submit" class="btn-submit">Confirmer ma réservation</button>
    </form>

    <a href="{{ route('reserver') }}" class="back-link">← Choisir un autre créneau</a>
@endsection
