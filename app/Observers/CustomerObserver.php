<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-25 10:05:30              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 namespace App\Observers; use App\Models\Customer; use App\Jobs\SyncCustomerToDbPusat; class CustomerObserver { public function created(Customer $Jyl61) { SyncCustomerToDbPusat::dispatch($Jyl61, 'create'); } public function updated(Customer $Jyl61) { SyncCustomerToDbPusat::dispatch($Jyl61, 'update'); } public function deleted(Customer $Jyl61) { SyncCustomerToDbPusat::dispatch($Jyl61, 'delete'); } }