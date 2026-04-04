<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Paul Hand Wash - Choisissez votre domaine</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-white shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <h1 class="text-3xl font-bold text-gray-900 text-center">
                    Paul Hand Wash
                </h1>
                <p class="text-center text-gray-600 mt-2">
                    Choisissez votre domaine
                </p>
            </div>
        </header>

        <!-- Domaines -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            @if($domaines->isEmpty())
                <div class="text-center py-12">
                    <p class="text-gray-500 text-lg">Aucun domaine disponible</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($domaines as $domaine)
                        <a href="/{{ $domaine->slug }}" 
                           class="block bg-white rounded-lg shadow hover:shadow-lg transition-shadow duration-200 overflow-hidden">
                            <div class="aspect-w-16 aspect-h-9 bg-gray-200">
                                @if($domaine->image)
                                    <img src="{{ asset($domaine->image) }}" 
                                         alt="{{ $domaine->name }}"
                                         class="w-full h-48 object-cover">
                                @else
                                    <div class="w-full h-48 flex items-center justify-center bg-gray-100">
                                        <span class="text-gray-400">{{ $domaine->name }}</span>
                                    </div>
                                @endif
                            </div>
                            <div class="p-6">
                                <h2 class="text-xl font-semibold text-gray-900">
                                    {{ $domaine->name }}
                                </h2>
                                @if($domaine->description)
                                    <p class="mt-2 text-gray-600 text-sm">
                                        {{ $domaine->description }}
                                    </p>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </main>
    </div>
</body>
</html>
