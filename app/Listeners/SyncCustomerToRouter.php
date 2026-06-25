<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-25 10:05:25              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 namespace App\Listeners; use App\Events\CustomerCreated; use App\Models\AuditLog; use Illuminate\Contracts\Queue\ShouldQueue; use Illuminate\Queue\InteractsWithQueue; use Illuminate\Support\Facades\Log; class SyncCustomerToRouter implements ShouldQueue { use InteractsWithQueue; public function handle(CustomerCreated $Bv2x0) { goto t0Kvx; a_hpa: try { Log::info("Syncing Customer {$OvNqc->username} to Router {$OvNqc->router_id}"); AuditLog::create(['tenant_id' => $OvNqc->tenant_id, 'action' => 'SYNC', 'module' => 'MikroTik', 'description' => "Berhasil sinkronisasi PPPoE {$OvNqc->username} ke Router {$OvNqc->router_id}.", 'user_id' => null]); } catch (\Exception $a8y4n) { Log::error("Failed to sync customer to router: " . $a8y4n->getMessage()); } goto BkyHK; EY9IV: if (!$Bv2x0->shouldSyncPppoe) { return; } goto Jq4Lk; t0Kvx: $OvNqc = $Bv2x0->customer; goto EY9IV; Jq4Lk: if (!$OvNqc->router_id) { return; } goto a_hpa; BkyHK: } }