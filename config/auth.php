<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-26 22:34:37              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 use App\Models\User; return ['defaults' => ['guard' => env('AUTH_GUARD', 'web'), 'passwords' => env('AUTH_PASSWORD_BROKER', 'users')], 'guards' => ['web' => ['driver' => 'session', 'provider' => 'users']], 'providers' => ['users' => ['driver' => 'eloquent', 'model' => env('AUTH_MODEL', User::class)]], 'passwords' => ['users' => ['provider' => 'users', 'table' => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'), 'expire' => 60, 'throttle' => 60]], 'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800)];
