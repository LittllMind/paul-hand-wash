<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paul Hand Wash - @yield('title', 'Paiement')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <nav class="bg-white shadow-sm">
        <div class="container mx-auto px-4 py-4">
            <a href="{{ route('home') }}" class="text-xl font-bold text-blue-600">
                Paul Hand Wash
            </a>
        </div>
    </nav>

    <main>
        @yield('content')
    </main>
</body>
</html>
