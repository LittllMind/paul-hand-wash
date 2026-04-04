<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un Événement - Paul Hand Wash</title>
</head>
<body>
    <h1>Créer un nouvel Événement</h1>

    <form action="{{ route('admin.evenements.store') }}" method="POST">
        @csrf

        <div>
            <label for="titre">Titre</label>
            <input type="text" name="titre" id="titre" value="{{ old('titre') }}" required>
            @error('titre')
                <span>{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label for="description">Description</label>
            <textarea name="description" id="description">{{ old('description') }}</textarea>
            @error('description')
                <span>{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label for="date_debut">Date de début</label>
            <input type="datetime-local" name="date_debut" id="date_debut" value="{{ old('date_debut') }}">
            @error('date_debut')
                <span>{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label for="date_fin">Date de fin</label>
            <input type="datetime-local" name="date_fin" id="date_fin" value="{{ old('date_fin') }}">
            @error('date_fin')
                <span>{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label for="lieu_id">Lieu</label>
            <select name="lieu_id" id="lieu_id" required>
                <option value="">-- Sélectionner un lieu --</option>
                @foreach($lieux as $lieu)
                    <option value="{{ $lieu->id }}" {{ old('lieu_id') == $lieu->id ? 'selected' : '' }}>
                        {{ $lieu->nom }}
                    </option>
                @endforeach
            </select>
            @error('lieu_id')
                <span>{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label for="categorie_id">Catégorie</label>
            <select name="categorie_id" id="categorie_id">
                <option value="">-- Sélectionner une catégorie --</option>
                @foreach($categories as $categorie)
                    <option value="{{ $categorie->id }}" {{ old('categorie_id') == $categorie->id ? 'selected' : '' }}>
                        {{ $categorie->nom }}
                    </option>
                @endforeach
            </select>
            @error('categorie_id')
                <span>{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label for="places_limite">Nombre de places (optionnel)</label>
            <input type="number" name="places_limite" id="places_limite" value="{{ old('places_limite') }}" min="1">
            @error('places_limite')
                <span>{{ $message }}</span>
            @enderror
        </div>

        <button type="submit">Créer</button>
        <a href="{{ route('admin.evenements.index') }}">Annuler</a>
    </form>
</body>
</html>
