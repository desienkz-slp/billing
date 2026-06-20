<?php
// Quick server audit script - run via: php artisan tinker < /tmp/audit_roles.php
require_once '/var/www/ladapala-bill/vendor/autoload.php';
$app = require_once '/var/www/ladapala-bill/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Check 1: Can we query roles?
echo "=== CHECK 1: Role Query ===\n";
try {
    $roles = \App\Models\Role::withCount('users')->orderBy('name')->get();
    echo "OK - Found " . $roles->count() . " roles\n";
    foreach ($roles as $r) {
        echo "  - {$r->name} (users: {$r->users_count})\n";
    }
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

// Check 2: Does Role have fee columns?
echo "\n=== CHECK 2: Fee Columns ===\n";
try {
    $columns = \Illuminate\Support\Facades\Schema::getColumnListing('roles');
    $feeColumns = array_filter($columns, fn($c) => str_contains($c, 'fee'));
    echo "Fee columns: " . implode(', ', $feeColumns) . "\n";
    echo "All columns: " . implode(', ', $columns) . "\n";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

// Check 3: Check the Roles JS file exists and has content
echo "\n=== CHECK 3: JS Asset ===\n";
$manifest = json_decode(file_get_contents('/var/www/ladapala-bill/public/build/manifest.json'), true);
$rolesEntry = $manifest['resources/js/Pages/Settings/Roles.vue'] ?? null;
if ($rolesEntry) {
    $jsFile = '/var/www/ladapala-bill/public/build/' . $rolesEntry['file'];
    echo "JS file: {$rolesEntry['file']}\n";
    echo "Exists: " . (file_exists($jsFile) ? 'YES' : 'NO') . "\n";
    echo "Size: " . filesize($jsFile) . " bytes\n";
    
    // Check for obvious errors in the JS
    $jsContent = file_get_contents($jsFile);
    echo "Contains 'permissionGroups': " . (str_contains($jsContent, 'permissionGroups') ? 'YES' : 'NO') . "\n";
    echo "Contains 'can_view_dashboard': " . (str_contains($jsContent, 'can_view_dashboard') ? 'YES' : 'NO') . "\n";
    echo "Contains 'renderError': " . (str_contains($jsContent, 'renderError') ? 'YES' : 'NO') . "\n";
} else {
    echo "ERROR: Roles.vue not in manifest!\n";
}

// Check 4: CSS file
echo "\n=== CHECK 4: CSS Asset ===\n";
if (isset($rolesEntry['css'])) {
    foreach ($rolesEntry['css'] as $css) {
        $cssFile = '/var/www/ladapala-bill/public/build/' . $css;
        echo "CSS file: {$css}, Exists: " . (file_exists($cssFile) ? 'YES' : 'NO') . "\n";
    }
}

echo "\n=== CHECK 5: Laravel Error Log (last 5 errors) ===\n";
$logFile = '/var/www/ladapala-bill/storage/logs/laravel.log';
if (file_exists($logFile)) {
    $lines = file($logFile);
    $errorLines = array_filter($lines, fn($l) => str_contains($l, 'local.ERROR'));
    $lastErrors = array_slice($errorLines, -5);
    foreach ($lastErrors as $l) {
        echo substr(trim($l), 0, 200) . "\n";
    }
    if (empty($lastErrors)) echo "No recent errors.\n";
}
