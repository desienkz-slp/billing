<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-26 22:34:35              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 namespace App\Listeners; use App\Events\CustomerCreated; use App\Models\AuditLog; use Illuminate\Contracts\Queue\ShouldQueue; use Illuminate\Queue\InteractsWithQueue; use Illuminate\Support\Facades\Log; class SendCustomerCreationNotification implements ShouldQueue { use InteractsWithQueue; public function handle(CustomerCreated $LXrNL) { $ODChU = $LXrNL->customer; try { AuditLog::create(['tenant_id' => $ODChU->tenant_id, 'action' => 'CREATE', 'module' => 'Customer', 'description' => "Menambahkan pelanggan baru: {$ODChU->name} (ID: {$ODChU->id})", 'user_id' => auth()->id() ?? null]); Log::info("FCM Notification sent for new customer {$ODChU->name}"); } catch (\Exception $Ge4VM) { Log::error("Failed to send customer notification: " . $Ge4VM->getMessage()); } } }
