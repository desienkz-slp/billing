<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-27 04:10:42              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 goto xFnh7; BLUsO: if (!$qgKaz) { echo "No server found\n"; exit; } goto JOQMT; JOQMT: $HMFmi = app(\App\Services\RadiusService::class); goto VW3_R; xGI9i: $nMBt3 = require_once 'bootstrap/app.php'; goto RP1Dc; K4yr4: $PS3IT->bootstrap(); goto dfNCt; RP1Dc: $PS3IT = $nMBt3->make(Illuminate\Contracts\Console\Kernel::class); goto K4yr4; xFnh7: require 'vendor/autoload.php'; goto xGI9i; dfNCt: $qgKaz = \App\Models\Server::where('type', 'upluk_upluk_api')->first(); goto BLUsO; VW3_R: print_r($HMFmi->testConnection($qgKaz));
