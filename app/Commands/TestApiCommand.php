<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-27 01:21:30              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 namespace App\Console\Commands; use Illuminate\Console\Command; use Illuminate\Support\Facades\Http; class TestApiCommand extends Command { protected $signature = 'test:api'; protected $description = 'Test API'; public function handle() { goto hpz4U; Ada2o: $ZM16z = Http::withToken($qHdEa)->get('http://localhost/api/v1/customers/dashboard-stats?month=6&year=2026'); goto vsgDa; RT9hP: $qHdEa = $Ct6TI->json('data.token'); goto fmOSm; hpz4U: $Ct6TI = Http::post('http://localhost/api/v1/login', ['username' => 'sysadmin', 'password' => 'password']); goto RT9hP; vsgDa: $this->info("No filter: " . json_encode($ZM16z->json())); goto cNWBk; hC_nb: $this->info("Filter: " . json_encode($UIi7N->json())); goto RUNco; fmOSm: $this->info("Token: {$qHdEa}"); goto Ada2o; cNWBk: $UIi7N = Http::withToken($qHdEa)->get('http://localhost/api/v1/customers/dashboard-stats?month=6&year=2026&billing_start=18&billing_end=19'); goto hC_nb; RUNco: } }
