<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un Lieu - Paul Hand Wash</title>
</head>
<body>
    <h1>Créer un nouveau Lieu</h1>

    <form action="{{ route('admin.lieux.store') }}" method="POST">
        @csrf

        <div>
            <label for="nom">Nom</label>
            <input type="text" name="nom" id="nom" value="{{ old('nom') }}" required>
            @error('nom')
                <span>{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label for="adresse">Adresse</label>
            <textarea name="adresse" id="adresse" required>{{ old('adresse') }}</textarea>
            @error('adresse')
                <span>{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label for="ville">Ville</label>
            <input type="text" name="ville" id="ville" value="{{ old('ville') }}" required>
            @error('ville')
                <span>{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label for="code_postal">Code Postal</label>
            <input type="text" name="code_postal" id="code_postal" value="{{ old('code_postal') }}" required>
            @error('code_postal')
                <span>{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label for="latitude">Latitude</label>
            <input type="number" step="any" name="latitude" id="latitude" value="{{ old('latitude') }}">
            @error('latitude')
                <span>{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label for="longitude">Longitude</label>
            <input type="number" step="any" name="longitude" id="longitude" value="{{ old('longitude') }}">
            @error('longitude')
                <span>{{ $message }}</span>
            @enderror
        </div>

        <button type="submit">Créer</button>
        <a href="{{ route('admin.lieux.index') }}">Annuler</a>
    </form>
</body>
</html>
