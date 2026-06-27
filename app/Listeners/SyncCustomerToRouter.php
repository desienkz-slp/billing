<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-26 22:34:36              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 namespace App\Listeners; use App\Events\CustomerCreated; use App\Models\AuditLog; use Illuminate\Contracts\Queue\ShouldQueue; use Illuminate\Queue\InteractsWithQueue; use Illuminate\Support\Facades\Log; class SyncCustomerToRouter implements ShouldQueue { use InteractsWithQueue; public function handle(CustomerCreated $LXrNL) { goto yiqci; Kg48L: try { Log::info("Syncing Customer {$ODChU->username} to Router {$ODChU->router_id}"); AuditLog::create(['tenant_id' => $ODChU->tenant_id, 'action' => 'SYNC', 'module' => 'MikroTik', 'description' => "Berhasil sinkronisasi PPPoE {$ODChU->username} ke Router {$ODChU->router_id}.", 'user_id' => null]); } catch (\Exception $Ge4VM) { Log::error("Failed to sync customer to router: " . $Ge4VM->getMessage()); } goto JYds6; yiqci: $ODChU = $LXrNL->customer; goto o2heA; o2heA: if (!$LXrNL->shouldSyncPppoe) { return; } goto rmt_F; rmt_F: if (!$ODChU->router_id) { return; } goto Kg48L; JYds6: } }
