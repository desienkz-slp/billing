<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$router = \App\Models\Router::first();
echo "Router: {$router->name}\n";
$service = new \App\Services\MikroTik\RouterConfigService();
try {
    echo "Attempting FTP Export...\n";
    $res = $service->exportViaFtp($router);
    echo "Length: ".strlen($res)."\n";
} catch (\Throwable $e) {
    echo "Exception: ".$e->getMessage()."\n";
}
