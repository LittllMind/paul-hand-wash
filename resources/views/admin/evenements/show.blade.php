<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détail Événement - Paul Hand Wash</title>
</head>
<body>
    <h1>Détail de l'Événement</h1>

    <div>
        <h2>{{ $evenement->titre }}</h2>

        <p><strong>Description:</strong> {{ $evenement->description ?? 'Aucune description' }}</p>
        <p><strong>Lieu:</strong> {{ $evenement->lieu ? $evenement->lieu->nom : 'Non défini' }}</p>
        <p><strong>Catégorie:</strong> {{ $evenement->categorie ? $evenement->categorie->nom : 'Non défini' }}</p>
        <p><strong>Date de début:</strong> {{ $evenement->date_debut ? $evenement->date_debut->format('d/m/Y H:i') : 'Non définie' }}</p>
        <p><strong>Date de fin:</strong> {{ $evenement->date_fin ? $evenement->date_fin->format('d/m/Y H:i') : 'Non définie' }}</p>
        <p><strong>Créé le:</strong> {{ $evenement->created_at->format('d/m/Y H:i') }}</p>
        <p><strong>Mis à jour le:</strong> {{ $evenement->updated_at->format('d/m/Y H:i') }}</p>
    </div>

    <div>
        <a href="{{ route('admin.evenements.edit', $evenement) }}">Modifier</a> |
        <a href="{{ route('admin.evenements.index') }}">Retour à la liste</a>
    </div>
</body>
</html>
