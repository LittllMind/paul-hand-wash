@extends('layouts.front')

@section('meta')
    <title>Réserver un lavage - Paolo Wash | Lavage auto à domicile</title>
    <meta name="description" content="Réservez votre créneau de lavage auto à domicile avec Paolo Wash. Choisissez votre date et lieu de rendez-vous.">
    <meta name="keywords" content="réservation, lavage auto, créneau, Paolo Wash, domicile">
    <link rel="canonical" href="{{ route('reserver') }}">
    
    <meta property="og:title" content="Réserver un lavage - Paolo Wash">
    <meta property="og:description" content="Réservez votre créneau de lavage auto à domicile.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ route('reserver') }}">
    <meta property="og:site_name" content="Paolo Wash">
    
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="Réserver un lavage - Paolo Wash">
    <meta name="twitter:description" content="Réservez votre créneau de lavage auto à domicile.">
@endsection

@section('content')
    <h1>🚗 Réserver un lavage</h1>

    <div class="filters">
        <form method="GET" action="{{ route('reserver') }}">
            <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                <div>
                    <label>Lieu:</label>
                    <select name="lieu_id" onchange="this.form.submit()">
                        <option value="">Tous les lieux</option>
                        @foreach($lieux as $lieu)
                            <option value="{{ $lieu->id }}" {{ request('lieu_id') == $lieu->id ? 'selected' : '' }}>
                                {{ $lieu->nom }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label>Date:</label>
                    <input type="date" name="date" value="{{ request('date') }}" onchange="this.form.submit()">
                </div>

                <div>
                    <a href="{{ route('reserver') }}" style="color: #666;">Réinitialiser</a>
                </div>
            </div>
        </form>
    </div>

    @if($creneauxParDate->isEmpty())
        <div class="empty">
            <p>😔 Aucun créneau disponible pour le moment.</p>
            <p>Essayez avec d'autres filtres ou revenez plus tard.</p>
        </div>
    @else
        @foreach($creneauxParDate as $date => $creneauxJour)
            <div class="date-section">
                <div class="date-header">
                    📅 {{ \Carbon\Carbon::parse($date)->translatedFormat('l d F Y') }}
                </div>

                @foreach($creneauxJour as $creneau)
                    <div class="slot">
                        <div class="slot-info">
                            <div>
                                <div class="slot-time">
                                    🕐 {{ $creneau->heure_debut->format('H:i') }} - {{ $creneau->heure_fin->format('H:i') }}
                                </div>
                                <div class="slot-lieu">
                                    📍 {{ $creneau->lieu->nom }}
                                </div>
                            </div>
                            <a href="{{ route('reserver.show', $creneau) }}" class="btn-reserver">Choisir ce créneau →</a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endforeach

        <div style="margin-top: 20px;">
            {{ $creneaux->links() }}
        </div>
    @endif
@endsection
