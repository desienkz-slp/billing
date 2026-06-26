<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-27 01:21:30              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 namespace App\Console\Commands; use Illuminate\Console\Command; use App\Models\Tenant; use App\Models\User; use Illuminate\Support\Str; class MakeSysadmin extends Command { protected $signature = 'make:sysadmin'; protected $description = 'Create a default system admin user for tenant management'; public function handle() { goto iBEKl; m0MY8: $this->info('Success! Sysadmin user is ready.'); goto KskUq; sMGwP: $this->line('Username: sysadmin'); goto wKPmE; iBEKl: $this->info('Creating System Tenant...'); goto WSSkG; IV4Q9: $this->line('---------------------------------'); goto pWoTu; r5Tqn: $l8Pdr = User::withoutGlobalScopes()->updateOrCreate(['username' => 'sysadmin'], ['tenant_id' => $EUm2s->id, 'name' => 'System Administrator', 'email' => 'sysadmin@system.local', 'password' => 'sysadmin', 'is_system_admin' => true, 'is_active' => true]); goto m0MY8; U50pE: $this->info('Creating Sysadmin User...'); goto r5Tqn; pWoTu: $this->line('Login directly to access the superadmin panel.'); goto l5n3a; KskUq: $this->line('---------------------------------'); goto sMGwP; WSSkG: $EUm2s = Tenant::firstOrCreate(['slug' => 'system'], ['name' => 'System', 'uuid' => (string) Str::uuid(), 'is_active' => true]); goto U50pE; wKPmE: $this->line('Password: sysadmin'); goto IV4Q9; l5n3a: } }
