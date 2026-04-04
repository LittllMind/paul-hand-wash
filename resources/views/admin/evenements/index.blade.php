<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Événements - Paul Hand Wash</title>
</head>
<body>
    <h1>Liste des Événements</h1>

    @if(session('success'))
        <div style="color: green;">{{ session('success') }}</div>
    @endif

    <a href="{{ route('admin.evenements.create') }}">+ Nouvel Événement</a>

    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Titre</th>
                <th>Lieu</th>
                <th>Catégorie</th>
                <th>Date début</th>
                <th>Date fin</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($evenements as $evenement)
                <tr>
                    <td>{{ $evenement->id }}</td>
                    <td>{{ $evenement->titre }}</td>
                    <td>{{ $evenement->lieu ? $evenement->lieu->nom : 'N/A' }}</td>
                    <td>{{ $evenement->categorie ? $evenement->categorie->nom : 'N/A' }}</td>
                    <td>{{ $evenement->date_debut ? $evenement->date_debut->format('d/m/Y H:i') : 'N/A' }}</td>
                    <td>{{ $evenement->date_fin ? $evenement->date_fin->format('d/m/Y H:i') : 'N/A' }}</td>
                    <td>
                        <a href="{{ route('admin.evenements.show', $evenement) }}">Voir</a> |
                        <a href="{{ route('admin.evenements.edit', $evenement) }}">Modifier</a>
                        <form action="{{ route('admin.evenements.destroy', $evenement) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Supprimer cet événement ?')">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">Aucun événement trouvé</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{ $evenements->links() }}
</body>
</html>
