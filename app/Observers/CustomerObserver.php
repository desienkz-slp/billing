<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-27 01:21:32              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 namespace App\Observers; use App\Models\Customer; use App\Jobs\SyncCustomerToDbPusat; class CustomerObserver { public function created(Customer $RX5oA) { SyncCustomerToDbPusat::dispatch($RX5oA, 'create'); } public function updated(Customer $RX5oA) { SyncCustomerToDbPusat::dispatch($RX5oA, 'update'); } public function deleted(Customer $RX5oA) { SyncCustomerToDbPusat::dispatch($RX5oA, 'delete'); } }
