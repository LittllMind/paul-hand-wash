<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

// Force load routes
$router = $app->make('router');
require __DIR__.'/routes/web.php';

echo "\n=== After manual route load ===\n";
$routes = Route::getRoutes();
foreach ($routes as $route) {
    $methods = implode('|', $route->methods());
    $name = $route->getName() ?: 'no name';
    echo "[$methods] {$route->uri()} => {$name}\n";
}

// Simulate a POST request to /admin/lieux
$request = Illuminate\Http\Request::create('/admin/lieux', 'POST', [
    'nom' => 'Test',
    'adresse' => 'Test',
    'ville' => 'Test',
    'code_postal' => '12345',
]);

$app->instance('request', $request);

try {
    $response = $kernel->handle($request);
    echo "Response status: " . $response->getStatusCode() . "\n";
    echo "Response content: " . substr($response->getContent(), 0, 500) . "\n";
} catch (Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}

// Check registered routes
echo "\n=== All registered routes ===\n";
$routes = Route::getRoutes();
foreach ($routes as $route) {
    $methods = implode('|', $route->methods());
    $name = $route->getName() ?: 'no name';
    echo "[$methods] {$route->uri()} => {$name}\n";
}
