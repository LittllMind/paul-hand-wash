<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Domaines - Paul Hand Wash</title>
</head>
<body>
    <h1>Liste des Domaines</h1>

    @if(session('success'))
        <div style="color: green;">{{ session('success') }}</div>
    @endif

    <a href="{{ route('admin.domaines.create') }}">+ Nouveau Domaine</a>
    <a href="{{ route('home') }}">← Retour au site</a>

    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Slug</th>
                <th>Nom</th>
                <th>Description</th>
                <th>Image</th>
                <th>Actif</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($domaines as $domaine)
                <tr>
                    <td>{{ $domaine->id }}</td>
                    <td>{{ $domaine->slug }}</td>
                    <td>{{ $domaine->name }}</td>
                    <td>{{ Str::limit($domaine->description, 50) ?? '-' }}</td>
                    <td>
                        @if($domaine->image)
                            <img src="{{ asset($domaine->image) }}" alt="" style="max-width: 50px; max-height: 50px;">
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        @if($domaine->active)
                            <span style="color: green;">✓ Actif</span>
                        @else
                            <span style="color: red;">✗ Inactif</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.domaines.edit', $domaine) }}">Modifier</a>
                        <form action="{{ route('admin.domaines.destroy', $domaine) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Supprimer ce domaine ?')">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">Aucun domaine trouvé</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{ $domaines->links() }}
</body>
</html>
