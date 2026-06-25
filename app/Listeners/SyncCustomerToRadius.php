<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-25 10:05:25              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 namespace App\Listeners; use App\Events\CustomerCreated; use Illuminate\Contracts\Queue\ShouldQueue; use Illuminate\Queue\InteractsWithQueue; use Illuminate\Support\Facades\Log; class SyncCustomerToRadius implements ShouldQueue { use InteractsWithQueue; public function handle(CustomerCreated $UGIWI) { goto l2dM6; c4Zlr: if (!$UGIWI->shouldSyncPppoe) { return; } goto R0XiH; l2dM6: $Y4iRB = $UGIWI->customer; goto c4Zlr; AOL5S: try { Log::info("Syncing Customer {$Y4iRB->username} to RADIUS DB (radcheck/radusergroup)"); } catch (\Exception $BNLTI) { Log::error("Failed to sync customer to RADIUS: " . $BNLTI->getMessage()); } goto BhVuy; R0XiH: if (!$Y4iRB->server_id) { return; } goto AOL5S; BhVuy: } }