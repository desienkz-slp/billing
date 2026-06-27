<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-26 22:34:36              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 namespace App\Listeners; use App\Events\CustomerCreated; use Illuminate\Contracts\Queue\ShouldQueue; use Illuminate\Queue\InteractsWithQueue; use Illuminate\Support\Facades\Log; class SyncCustomerToRadius implements ShouldQueue { use InteractsWithQueue; public function handle(CustomerCreated $LXrNL) { goto FDw8W; Xdp3Z: if (!$ODChU->server_id) { return; } goto c7Rrk; FDw8W: $ODChU = $LXrNL->customer; goto t81ax; c7Rrk: try { Log::info("Syncing Customer {$ODChU->username} to RADIUS DB (radcheck/radusergroup)"); } catch (\Exception $Ge4VM) { Log::error("Failed to sync customer to RADIUS: " . $Ge4VM->getMessage()); } goto BxfK1; t81ax: if (!$LXrNL->shouldSyncPppoe) { return; } goto Xdp3Z; BxfK1: } }
