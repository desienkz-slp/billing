<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$server = \App\Models\Server::where('type', 'upluk_upluk_api')->first();
if (!$server) {
    echo "No server found\n";
    exit;
}
$service = app(\App\Services\RadiusService::class);
print_r($service->testConnection($server));
