@props([
    'title' => config('app.name', 'Paolo Wash'),
    'description' => 'Paolo Wash - Service de lavage auto à domicile. Votre voiture étincelante, sans bouger.',
    'keywords' => 'lavage auto, domicile, voiture, detailing, Paolo Wash, rénovation automobile',
    'canonical' => null,
    'ogImage' => null,
    'ogType' => 'website',
])

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    {{-- Meta tags de base --}}
    <title>{{ $title }}</title>
    <meta name="description" content="{{ $description }}">
    <meta name="keywords" content="{{ $keywords }}">
    <meta name="author" content="Paolo Wash">
    <meta name="robots" content="index, follow">
    
    {{-- Canonical URL --}}
    @if($canonical)
        <link rel="canonical" href="{{ $canonical }}">
    @else
        <link rel="canonical" href="{{ url()->current() }}">
    @endif
    
    {{-- OpenGraph / Facebook --}}
    <meta property="og:title" content="{{ $title }}">
    <meta property="og:description" content="{{ $description }}">
    <meta property="og:type" content="{{ $ogType }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content="Paolo Wash">
    <meta property="og:locale" content="fr_FR">
    @if($ogImage)
        <meta property="og:image" content="{{ $ogImage }}">
    @else
        <meta property="og:image" content="{{ asset('images/og-default.jpg') }}">
    @endif
    
    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $title }}">
    <meta name="twitter:description" content="{{ $description }}">
    @if($ogImage)
        <meta name="twitter:image" content="{{ $ogImage }}">
    @else
        <meta name="twitter:image" content="{{ asset('images/og-default.jpg') }}">
    @endif
    
    {{-- Favicon --}}
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    
    {{-- Tailwind CSS --}}
    <script src="https://cdn.tailwindcss.com"></script>
    
    {{-- Stack pour CSS additionnel --}}
    @stack('styles')
</head>
<body class="bg-gray-50">
    {{ $slot }}
    
    {{-- Stack pour JS additionnel --}}
    @stack('scripts')
</body>
</html>
