@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">Modifier le lieu</h1>

    <form action="{{ route('lieux.update', $lieu) }}" method="POST" class="max-w-lg">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label for="nom" class="block text-gray-700 font-bold mb-2">Nom</label>
            <input type="text" name="nom" id="nom" value="{{ old('nom', $lieu->nom) }}"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('nom') border-red-500 @enderror">
            @error('nom')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="adresse" class="block text-gray-700 font-bold mb-2">Adresse</label>
            <input type="text" name="adresse" id="adresse" value="{{ old('adresse', $lieu->adresse) }}"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('adresse') border-red-500 @enderror">
            @error('adresse')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="code_postal" class="block text-gray-700 font-bold mb-2">Code Postal</label>
            <input type="text" name="code_postal" id="code_postal" value="{{ old('code_postal', $lieu->code_postal) }}"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('code_postal') border-red-500 @enderror">
            @error('code_postal')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="ville" class="block text-gray-700 font-bold mb-2">Ville</label>
            <input type="text" name="ville" id="ville" value="{{ old('ville', $lieu->ville) }}"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('ville') border-red-500 @enderror">
            @error('ville')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label for="pays" class="block text-gray-700 font-bold mb-2">Pays</label>
            <input type="text" name="pays" id="pays" value="{{ old('pays', $lieu->pays) }}"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('pays') border-red-500 @enderror">
            @error('pays')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-between">
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Mettre à jour
            </button>
            <a href="{{ route('lieux.show', $lieu) }}" class="text-gray-600 hover:text-gray-800">
                Annuler
            </a>
        </div>
    </form>
</div>
@endsection
