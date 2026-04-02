<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Catégories - Paul Hand Wash</title>
</head>
<body>
    <h1>Liste des Catégories</h1>

    @if(session('success'))
        <div style="color: green;">{{ session('success') }}</div>
    @endif

    <a href="{{ route('admin.categories.create') }}">+ Nouvelle Catégorie</a>

    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Description</th>
                <th>Couleur</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($categories as $categorie)
                <tr>
                    <td>{{ $categorie->id }}</td>
                    <td>{{ $categorie->nom }}</td>
                    <td>{{ $categorie->description ?? '-' }}</td>
                    <td>
                        @if($categorie->couleur)
                            <span style="background-color: {{ $categorie->couleur }}; padding: 4px 8px; color: white; border-radius: 4px;">
                                {{ $categorie->couleur }}
                            </span>
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.categories.edit', $categorie) }}">Modifier</a>
                        <form action="{{ route('admin.categories.destroy', $categorie) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Supprimer cette catégorie ?')">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">Aucune catégorie trouvée</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{ $categories->links() }}
</body>
</html>
