<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendrier des Présences - Paul Hand Wash</title>
    <style>
        .calendar { display: grid; grid-template-columns: repeat(7, 1fr); gap: 5px; }
        .day { border: 1px solid #ccc; padding: 10px; min-height: 100px; }
        .day-header { background: #f0f0f0; font-weight: bold; text-align: center; padding: 5px; }
        .slot { background: #e3f2fd; margin: 2px 0; padding: 2px; font-size: 0.8em; }
        .slot.reserved { background: #ffcdd2; }
    </style>
</head>
<body>
    <h1>Calendrier des Présences</h1>

    @if(session('success'))
        <div style="color: green;">{{ session('success') }}</div>
    @endif

    <form method="GET" action="{{ route('admin.presences.index') }}">
        <label>Mois:</label>
        <input type="month" name="mois" value="{{ $mois ?? now()->format('Y-m') }}">
        
        <label>Lieu:</label>
        <select name="lieu_id">
            <option value="">Tous</option>
            @foreach($lieux as $lieu)
                <option value="{{ $lieu->id }}" {{ request('lieu_id') == $lieu->id ? 'selected' : '' }}>
                    {{ $lieu->nom }}
                </option>
            @endforeach
        </select>
        
        <button type="submit">Filtrer</button>
    </form>

    <h2>{{ $titreMois ?? 'Mois courant' }}</h2>

    <div class="calendar">
        <div class="day-header">Lun</div>
        <div class="day-header">Mar</div>
        <div class="day-header">Mer</div>
        <div class="day-header">Jeu</div>
        <div class="day-header">Ven</div>
        <div class="day-header">Sam</div>
        <div class="day-header">Dim</div>

        @foreach($jours as $jour)
            <div class="day">
                <strong>{{ $jour['date']->format('d') }}</strong>
                @foreach($jour['creneaux'] as $creneau)
                    <div class="slot {{ $creneau->est_reserve ? 'reserved' : '' }}">
                        {{ $creneau->heure_debut->format('H:i') }} - {{ $creneau->heure_fin->format('H:i') }}
                        @if($creneau->est_reserve)
                            🔒 Réservé
                        @else
                            ✅ Disponible
                        @endif
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>

    <a href="{{ route('admin.lieux.index') }}">← Retour aux lieux</a> | 
    <a href="{{ route('admin.presences.batch') }}">+ Créer créneaux en batch</a>
</body>
</html>
