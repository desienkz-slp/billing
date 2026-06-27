<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-26 22:34:36              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 namespace App\Observers; use App\Models\Customer; use App\Jobs\SyncCustomerToDbPusat; class CustomerObserver { public function created(Customer $ODChU) { SyncCustomerToDbPusat::dispatch($ODChU, 'create'); } public function updated(Customer $ODChU) { SyncCustomerToDbPusat::dispatch($ODChU, 'update'); } public function deleted(Customer $ODChU) { SyncCustomerToDbPusat::dispatch($ODChU, 'delete'); } }
