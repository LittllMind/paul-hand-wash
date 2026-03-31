<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

// Run application to bootstrap everything
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// List all routes
$routes = collect(Route::getRoutes())->map(function ($route) {
    return [
        'name' => $route->getName(),
        'uri' => $route->uri(),
        'methods' => $route->methods(),
    ];
})->toArray();

$adminRoutes = array_filter($routes, fn($r) => str_contains($r['name'] ?? '', 'admin') || str_contains($r['uri'], 'admin'));

echo "=== Admin Routes ===\n";
foreach ($adminRoutes as $route) {
    echo "{$route['methods'][0]} {$route['uri']} [{$route['name']}]\n";
}

echo "\n=== Total Routes: " . count($routes) . " ===\n";
