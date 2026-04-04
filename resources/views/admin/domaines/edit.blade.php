<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un Domaine - Paul Hand Wash</title>
</head>
<body>
    <h1>Modifier le Domaine: {{ $domaine->name }}</h1>

    <form action="{{ route('admin.domaines.update', $domaine) }}" method="POST">
        @csrf
        @method('PUT')

        <div>
            <label for="slug">Slug (identifiant URL)</label>
            <input type="text" name="slug" id="slug" value="{{ old('slug', $domaine->slug) }}" required>
            @error('slug')
                <span style="color: red;">{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label for="name">Nom</label>
            <input type="text" name="name" id="name" value="{{ old('name', $domaine->name) }}" required>
            @error('name')
                <span style="color: red;">{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label for="description">Description</label>
            <textarea name="description" id="description" rows="3">{{ old('description', $domaine->description) }}</textarea>
            @error('description')
                <span style="color: red;">{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label for="image">Chemin de l'image</label>
            <input type="text" name="image" id="image" value="{{ old('image', $domaine->image) }}" placeholder="ex: images/domaines/mon-image.jpg">
            @error('image')
                <span style="color: red;">{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label>
                <input type="checkbox" name="active" value="1" {{ old('active', $domaine->active) ? 'checked' : '' }}>
                Actif (visible sur le site)
            </label>
        </div>

        <button type="submit">Mettre à jour</button>
        <a href="{{ route('admin.domaines.index') }}">Annuler</a>
    </form>
</body>
</html>
