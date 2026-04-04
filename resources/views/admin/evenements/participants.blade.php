<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Participants - {{ $evenement->titre }} - Paul Hand Wash</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f5f5f5; }
        .back-link { margin-top: 20px; }
    </style>
</head>
<body>
    <h1>Participants : {{ $evenement->titre }}</h1>

    <div>
        <p><strong>Date:</strong> {{ $evenement->date_debut ? $evenement->date_debut->format('d/m/Y H:i') : 'Non définie' }}</p>
        <p><strong>Total participants:</strong> {{ $evenement->inscriptionsCount() }}</p>
        @if($evenement->places_limite)
            <p><strong>Places disponibles:</strong> {{ $evenement->places_limite - $evenement->inscriptionsCount() }} / {{ $evenement->places_limite }}</p>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Nom</th>
                <th>Email</th>
                <th>Date d'inscription</th>
            </tr>
        </thead>
        <tbody>
            @forelse($evenement->users as $index => $participant)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $participant->name }}</td>
                    <td>{{ $participant->email }}</td>
                    <td>{{ $participant->pivot->inscrit_le ? \Carbon\Carbon::parse($participant->pivot->inscrit_le)->format('d/m/Y H:i') : 'N/A' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align: center;">Aucun participant inscrit.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="back-link">
        <a href="{{ route('admin.evenements.show', $evenement) }}">← Retour à l'événement</a> |
        <a href="{{ route('admin.evenements.index') }}">Retour à la liste</a>
    </div>
</body>
</html>
