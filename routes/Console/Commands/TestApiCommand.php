<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-26 20:40:49              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 namespace App\Console\Commands; use Illuminate\Console\Command; use Illuminate\Support\Facades\Http; class TestApiCommand extends Command { protected $signature = 'test:api'; protected $description = 'Test API'; public function handle() { goto Dqz3N; y28ZS: $mNprR = Http::withToken($BJQ_R)->get('http://localhost/api/v1/customers/dashboard-stats?month=6&year=2026&billing_start=18&billing_end=19'); goto PiKPD; umEBl: $this->info("Token: {$BJQ_R}"); goto F3q0G; PiKPD: $this->info("Filter: " . json_encode($mNprR->json())); goto AZmpa; F3q0G: $V_BkY = Http::withToken($BJQ_R)->get('http://localhost/api/v1/customers/dashboard-stats?month=6&year=2026'); goto dYIsm; Mmvgj: $BJQ_R = $ZijlW->json('data.token'); goto umEBl; dYIsm: $this->info("No filter: " . json_encode($V_BkY->json())); goto y28ZS; Dqz3N: $ZijlW = Http::post('http://localhost/api/v1/login', ['username' => 'sysadmin', 'password' => 'password']); goto Mmvgj; AZmpa: } }
