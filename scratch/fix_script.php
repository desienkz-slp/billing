<?php
$file = 'c:\xampp\htdocs\laravel-bill\scratch\check_console.js';
$content = file_get_contents($file);
$content = str_replace('input[type="email"]', '#login', $content);
$content = str_replace('input[type="password"]', '#password', $content);
file_put_contents($file, $content);
