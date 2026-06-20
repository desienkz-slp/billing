<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$sys = Auth::attempt(['username' => 'sysadmin', 'password' => 'sysadmin']);
echo 'Sysadmin auth successful: ' . ($sys ? 'YES' : 'NO') . "\n";
if ($sys) {
    $user = Auth::user();
    echo 'Sysadmin Redirect: ' . ($user->is_system_admin ? '/superadmin/tenants' : '/app-gateway') . "\n";
}

$sup = Auth::attempt(['username' => 'superadmin', 'password' => 'admin123']);
echo 'Superadmin auth successful: ' . ($sup ? 'YES' : 'NO') . "\n";
if ($sup) {
    $user = Auth::user();
    echo 'Superadmin Redirect: ' . ($user->is_system_admin ? '/superadmin/tenants' : '/app-gateway') . "\n";
}
