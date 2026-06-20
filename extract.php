<?php
$content = file_get_contents(__DIR__ . '/resources/views/layouts/app.blade.php');
preg_match('/<style>(.*?)<\/style>/s', $content, $matches);
file_put_contents(__DIR__ . '/resources/css/dashboard.css', $matches[1] ?? '');
