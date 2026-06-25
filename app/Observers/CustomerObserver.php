<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-25 10:49:52              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 namespace App\Observers; use App\Models\Customer; use App\Jobs\SyncCustomerToDbPusat; class CustomerObserver { public function created(Customer $ygIPj) { SyncCustomerToDbPusat::dispatch($ygIPj, 'create'); } public function updated(Customer $ygIPj) { SyncCustomerToDbPusat::dispatch($ygIPj, 'update'); } public function deleted(Customer $ygIPj) { SyncCustomerToDbPusat::dispatch($ygIPj, 'delete'); } }
