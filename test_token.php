<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$s = App\Models\Server::orderBy('id', 'desc')->first();
var_dump($s->getDecryptedApiToken());
