<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-27 01:21:32              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 namespace App\Listeners; use App\Events\CustomerCreated; use Illuminate\Contracts\Queue\ShouldQueue; use Illuminate\Queue\InteractsWithQueue; use Illuminate\Support\Facades\Log; class SyncCustomerToRadius implements ShouldQueue { use InteractsWithQueue; public function handle(CustomerCreated $A3CEf) { goto RqyOx; RqyOx: $RX5oA = $A3CEf->customer; goto v5Gpm; v5Gpm: if (!$A3CEf->shouldSyncPppoe) { return; } goto qR7GN; qR7GN: if (!$RX5oA->server_id) { return; } goto lD423; lD423: try { Log::info("Syncing Customer {$RX5oA->username} to RADIUS DB (radcheck/radusergroup)"); } catch (\Exception $UQk5V) { Log::error("Failed to sync customer to RADIUS: " . $UQk5V->getMessage()); } goto M_6xT; M_6xT: } }
