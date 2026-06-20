<?php
$c = file_get_contents('c:/xampp/htdocs/laravel-bill/public/css/statistik.css');
$replacements = [
    '#1e293b' => 'var(--bg-card)',
    'rgba(30, 41, 59, .5)' => 'var(--border)',
    '#161b22' => 'var(--bg-card)',
    '#94a3b8' => 'var(--text-muted)',
    '#cbd5e1' => 'var(--text-secondary)',
    '#e2e8f0' => 'var(--text-primary)',
    '#a5b4fc' => 'var(--accent)',
    'rgba(15, 23, 42, 0.6)' => 'var(--bg-card)',
    'rgba(255, 255, 255, 0.05)' => 'var(--border)',
    '#64748b' => 'var(--text-muted)'
];
$c = str_replace(array_keys($replacements), array_values($replacements), $c);
file_put_contents('c:/xampp/htdocs/laravel-bill/public/css/statistik.css', $c);

// Also fix index.blade.php
$c2 = file_get_contents('c:/xampp/htdocs/laravel-bill/resources/views/statistics/index.blade.php');
$replacements2 = [
    "color:'rgba(148,163,184,.06)'" => "color:'rgba(0,0,0,0.05)'",
    "color:'#e2e8f0'" => "color:'var(--text-primary)'",
    "color:'#94a3b8'" => "color:'var(--text-muted)'",
    "color:'#64748b'" => "color:'var(--text-secondary)'",
    "pointBackgroundColor:'#1e293b'" => "pointBackgroundColor:'var(--bg-card)'",
    "rgba(148,163,184,.15)" => "var(--border)"
];
$c2 = str_replace(array_keys($replacements2), array_values($replacements2), $c2);
file_put_contents('c:/xampp/htdocs/laravel-bill/resources/views/statistics/index.blade.php', $c2);
