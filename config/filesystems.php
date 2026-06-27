<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-26 22:34:37              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 return ['default' => env('FILESYSTEM_DISK', 'local'), 'disks' => ['local' => ['driver' => 'local', 'root' => storage_path('app/private'), 'serve' => true, 'throw' => false, 'report' => false], 'public' => ['driver' => 'local', 'root' => storage_path('app/public'), 'url' => rtrim(env('APP_URL', 'http://localhost'), '/') . '/storage', 'visibility' => 'public', 'throw' => false, 'report' => false], 's3' => ['driver' => 's3', 'key' => env('AWS_ACCESS_KEY_ID'), 'secret' => env('AWS_SECRET_ACCESS_KEY'), 'region' => env('AWS_DEFAULT_REGION'), 'bucket' => env('AWS_BUCKET'), 'url' => env('AWS_URL'), 'endpoint' => env('AWS_ENDPOINT'), 'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false), 'throw' => false, 'report' => false]], 'links' => [public_path('storage') => storage_path('app/public')]];
