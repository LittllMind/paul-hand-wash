<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un Domaine - Paul Hand Wash</title>
</head>
<body>
    <h1>Créer un nouveau Domaine</h1>

    <form action="{{ route('admin.domaines.store') }}" method="POST">
        @csrf

        <div>
            <label for="slug">Slug (identifiant URL)</label>
            <input type="text" name="slug" id="slug" value="{{ old('slug') }}" required placeholder="ex: savon-enfant">
            @error('slug')
                <span style="color: red;">{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label for="name">Nom</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required>
            @error('name')
                <span style="color: red;">{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label for="description">Description</label>
            <textarea name="description" id="description" rows="3">{{ old('description') }}</textarea>
            @error('description')
                <span style="color: red;">{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label for="image">Chemin de l'image</label>
            <input type="text" name="image" id="image" value="{{ old('image') }}" placeholder="ex: images/domaines/mon-image.jpg">
            @error('image')
                <span style="color: red;">{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label>
                <input type="checkbox" name="active" value="1" {{ old('active', true) ? 'checked' : '' }}>
                Actif (visible sur le site)
            </label>
        </div>

        <button type="submit">Créer</button>
        <a href="{{ route('admin.domaines.index') }}">Annuler</a>
    </form>
</body>
</html>
