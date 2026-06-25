<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-25 10:49:52              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 namespace App\Listeners; use App\Events\CustomerCreated; use Illuminate\Contracts\Queue\ShouldQueue; use Illuminate\Queue\InteractsWithQueue; use Illuminate\Support\Facades\Log; class SyncCustomerToRadius implements ShouldQueue { use InteractsWithQueue; public function handle(CustomerCreated $zUxeQ) { goto klxdr; klxdr: $ygIPj = $zUxeQ->customer; goto ebCL6; ebCL6: if (!$zUxeQ->shouldSyncPppoe) { return; } goto acQ3T; S5ie1: try { Log::info("Syncing Customer {$ygIPj->username} to RADIUS DB (radcheck/radusergroup)"); } catch (\Exception $a_tGM) { Log::error("Failed to sync customer to RADIUS: " . $a_tGM->getMessage()); } goto oGYVJ; acQ3T: if (!$ygIPj->server_id) { return; } goto S5ie1; oGYVJ: } }
