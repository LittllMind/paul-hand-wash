@component('mail::message')
# Confirmation d'inscription

Bonjour {{ $user->name }},

Vous êtes bien inscrit(e) à l'événement :

## {{ $evenement->titre }}

**Date de début :** {{ $evenement->date_debut->format('d/m/Y H:i') }}

**Date de fin :** {{ $evenement->date_fin->format('d/m/Y H:i') }}

**Lieu :** {{ $evenement->lieu->nom ?? 'Non précisé' }}

@component('mail::button', ['url' => route('admin.evenements.show', $evenement)])
Voir les détails de l'événement
@endcomponent

Merci pour votre inscription !

{{ config('app.name') }}
@endcomponent
