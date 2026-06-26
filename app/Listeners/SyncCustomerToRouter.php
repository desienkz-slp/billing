<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-27 01:21:32              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 namespace App\Listeners; use App\Events\CustomerCreated; use App\Models\AuditLog; use Illuminate\Contracts\Queue\ShouldQueue; use Illuminate\Queue\InteractsWithQueue; use Illuminate\Support\Facades\Log; class SyncCustomerToRouter implements ShouldQueue { use InteractsWithQueue; public function handle(CustomerCreated $A3CEf) { goto CDFkd; VpKh8: if (!$A3CEf->shouldSyncPppoe) { return; } goto nFIMy; CDFkd: $RX5oA = $A3CEf->customer; goto VpKh8; KeWJU: try { Log::info("Syncing Customer {$RX5oA->username} to Router {$RX5oA->router_id}"); AuditLog::create(['tenant_id' => $RX5oA->tenant_id, 'action' => 'SYNC', 'module' => 'MikroTik', 'description' => "Berhasil sinkronisasi PPPoE {$RX5oA->username} ke Router {$RX5oA->router_id}.", 'user_id' => null]); } catch (\Exception $UQk5V) { Log::error("Failed to sync customer to router: " . $UQk5V->getMessage()); } goto Xvu_q; nFIMy: if (!$RX5oA->router_id) { return; } goto KeWJU; Xvu_q: } }
