<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Paolo Wash">
    <meta name="robots" content="index, follow">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    @yield('meta')
    
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
        h1 { color: #333; }
        .filters { background: #f5f5f5; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        .date-section { margin: 20px 0; }
        .date-header { background: #2196F3; color: white; padding: 10px; border-radius: 4px; }
        .slot { border: 1px solid #ddd; padding: 15px; margin: 10px 0; border-radius: 4px; }
        .slot:hover { background: #f0f8ff; }
        .slot-info { display: flex; justify-content: space-between; align-items: center; }
        .slot-time { font-weight: bold; color: #2196F3; }
        .slot-lieu { color: #666; }
        .btn-reserver { background: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; }
        .empty { text-align: center; color: #999; padding: 40px; }
    </style>
    
    @stack('styles')
</head>
<body>
    @yield('content')
    
    <footer style="margin-top: 40px; text-align: center; color: #999;">
        <p>Paul Hand Wash - Lavage auto à domicile</p>
    </footer>
    
    @stack('scripts')
</body>
</html>
