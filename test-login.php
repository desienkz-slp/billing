<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$request = Illuminate\Http\Request::create('/login', 'POST', [
    'username' => 'sysadmin',
    'password' => 'sysadmin'
]);
$response = $kernel->handle($request);
echo 'Sysadmin Login Response Status: ' . $response->getStatusCode() . "\n";
echo 'Sysadmin Redirect Location: ' . $response->headers->get('Location') . "\n";

$request2 = Illuminate\Http\Request::create('/login', 'POST', [
    'username' => 'superadmin',
    'password' => 'admin123'
]);
$response2 = $kernel->handle($request2);
echo 'Superadmin Login Response Status: ' . $response2->getStatusCode() . "\n";
echo 'Superadmin Redirect Location: ' . $response2->headers->get('Location') . "\n";
