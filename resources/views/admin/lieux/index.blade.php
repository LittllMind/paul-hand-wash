<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Lieux - Paul Hand Wash</title>
</head>
<body>
    <h1>Liste des Lieux</h1>

    @if(session('success'))
        <div style="color: green;">{{ session('success') }}</div>
    @endif

    <a href="{{ route('admin.lieux.create') }}">+ Nouveau Lieu</a>

    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Adresse</th>
                <th>Ville</th>
                <th>Code Postal</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($lieux as $lieu)
                <tr>
                    <td>{{ $lieu->id }}</td>
                    <td>{{ $lieu->nom }}</td>
                    <td>{{ $lieu->adresse }}</td>
                    <td>{{ $lieu->ville }}</td>
                    <td>{{ $lieu->code_postal }}</td>
                    <td>
                        <a href="{{ route('admin.lieux.edit', $lieu) }}">Modifier</a>
                        <form action="{{ route('admin.lieux.destroy', $lieu) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Supprimer ce lieu ?')">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">Aucun lieu trouvé</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{ $lieux->links() }}
</body>
</html>
