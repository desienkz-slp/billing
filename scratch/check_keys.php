<?php
$content = file_get_contents('c:\xampp\htdocs\laravel-bill\resources\js\Pages\Settings\Roles.vue');

// Find all perm keys
preg_match_all("/\{ key: '([^']+)'/", $content, $matches);
$uiKeys = array_unique($matches[1]);

// Use a better regex for form keys to capture everything initialized
preg_match_all("/([a-z_]+):\s*(false|true|0|''|'none')/", $content, $formMatches);
$formKeys = [];
foreach($formMatches[1] as $key) {
    $formKeys[] = $key;
}
$formKeys = array_unique($formKeys);

$missing = array_diff($uiKeys, $formKeys);
echo "Missing keys in defaultForm:\n";
print_r($missing);
