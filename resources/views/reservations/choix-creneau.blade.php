<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réserver un lavage - Paul Hand Wash</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
        h1 { color: #333; }
        .filters { background: #f5f5f5; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        .date-section { margin: 20px 0; }
        .date-header { background: #2196F3; color: white; padding: 10px; border-radius: 4px; }
        .slot { border: 1px solid #ddd; padding: 15px; margin: 10px 0; border-radius: 4px; }
        .slot:hover { background: #f0f8ff; }
        .slot-info { display: flex; justify-content: space-between; align-items: center; }
        .slot-time { font-weight: bold; color: #2196F3; }
        .slot-lieu { color: #666; }
        .btn-reserver { background: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; }
        .empty { text-align: center; color: #999; padding: 40px; }
    </style>
</head>
<body>
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

    <footer style="margin-top: 40px; text-align: center; color: #999;">
        <p>Paul Hand Wash - Lavage auto à domicile</p>
    </footer>
</body>
</html>
