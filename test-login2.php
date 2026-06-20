<?php
require 'vendor/autoload.php';

$client = new \GuzzleHttp\Client(['cookies' => true]);

// 1. Get login page to get CSRF cookie and token
$response = $client->request('GET', 'http://127.0.0.1/login');
$html = (string) $response->getBody();

preg_match('/<meta name="csrf-token" content="([^"]+)">/', $html, $matches);
$csrfToken = $matches[1] ?? '';

echo "CSRF Token retrieved: " . substr($csrfToken, 0, 10) . "...\n";

// 2. Sysadmin Login
$sysadminRes = $client->request('POST', 'http://127.0.0.1/login', [
    'form_params' => [
        '_token' => $csrfToken,
        'username' => 'sysadmin',
        'password' => 'sysadmin'
    ],
    'allow_redirects' => false
]);
echo 'Sysadmin Login Response Status: ' . $sysadminRes->getStatusCode() . "\n";
echo 'Sysadmin Redirect Location: ' . $sysadminRes->getHeaderLine('Location') . "\n";

// Clear cookies for next login
$client = new \GuzzleHttp\Client(['cookies' => true]);
$response = $client->request('GET', 'http://127.0.0.1/login');
preg_match('/<meta name="csrf-token" content="([^"]+)">/', (string) $response->getBody(), $matches);
$csrfToken2 = $matches[1] ?? '';

// 3. Superadmin Login
$superadminRes = $client->request('POST', 'http://127.0.0.1/login', [
    'form_params' => [
        '_token' => $csrfToken2,
        'username' => 'superadmin',
        'password' => 'admin123'
    ],
    'allow_redirects' => false
]);
echo 'Superadmin Login Response Status: ' . $superadminRes->getStatusCode() . "\n";
echo 'Superadmin Redirect Location: ' . $superadminRes->getHeaderLine('Location') . "\n";
