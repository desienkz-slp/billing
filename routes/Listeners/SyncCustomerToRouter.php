<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-25 10:49:52              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 namespace App\Listeners; use App\Events\CustomerCreated; use App\Models\AuditLog; use Illuminate\Contracts\Queue\ShouldQueue; use Illuminate\Queue\InteractsWithQueue; use Illuminate\Support\Facades\Log; class SyncCustomerToRouter implements ShouldQueue { use InteractsWithQueue; public function handle(CustomerCreated $zUxeQ) { goto GLgM3; FT1Ly: try { Log::info("Syncing Customer {$ygIPj->username} to Router {$ygIPj->router_id}"); AuditLog::create(['tenant_id' => $ygIPj->tenant_id, 'action' => 'SYNC', 'module' => 'MikroTik', 'description' => "Berhasil sinkronisasi PPPoE {$ygIPj->username} ke Router {$ygIPj->router_id}.", 'user_id' => null]); } catch (\Exception $a_tGM) { Log::error("Failed to sync customer to router: " . $a_tGM->getMessage()); } goto MbHiZ; snBlA: if (!$zUxeQ->shouldSyncPppoe) { return; } goto MbiHd; MbiHd: if (!$ygIPj->router_id) { return; } goto FT1Ly; GLgM3: $ygIPj = $zUxeQ->customer; goto snBlA; MbHiZ: } }
