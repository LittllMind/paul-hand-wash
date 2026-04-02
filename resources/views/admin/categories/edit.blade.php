<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier une Catégorie - Paul Hand Wash</title>
</head>
<body>
    <h1>Modifier la Catégorie: {{ $categorie->nom }}</h1>

    <form action="{{ route('admin.categories.update', $categorie) }}" method="POST">
        @csrf
        @method('PUT')

        <div>
            <label for="nom">Nom</label>
            <input type="text" name="nom" id="nom" value="{{ old('nom', $categorie->nom) }}" required>
            @error('nom')
                <span>{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label for="description">Description</label>
            <textarea name="description" id="description">{{ old('description', $categorie->description) }}</textarea>
            @error('description')
                <span>{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label for="couleur">Couleur</label>
            <input type="text" name="couleur" id="couleur" value="{{ old('couleur', $categorie->couleur) }}" placeholder="#FF0000">
            @error('couleur')
                <span>{{ $message }}</span>
            @enderror
        </div>

        <button type="submit">Mettre à jour</button>
        <a href="{{ route('admin.categories.index') }}">Annuler</a>
    </form>
</body>
</html>
