<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-25 10:05:25              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 namespace App\Listeners; use App\Events\CustomerCreated; use App\Models\AuditLog; use Illuminate\Contracts\Queue\ShouldQueue; use Illuminate\Queue\InteractsWithQueue; use Illuminate\Support\Facades\Log; class SendCustomerCreationNotification implements ShouldQueue { use InteractsWithQueue; public function handle(CustomerCreated $t4m1Z) { $gCvqu = $t4m1Z->customer; try { AuditLog::create(['tenant_id' => $gCvqu->tenant_id, 'action' => 'CREATE', 'module' => 'Customer', 'description' => "Menambahkan pelanggan baru: {$gCvqu->name} (ID: {$gCvqu->id})", 'user_id' => auth()->id() ?? null]); Log::info("FCM Notification sent for new customer {$gCvqu->name}"); } catch (\Exception $ieblE) { Log::error("Failed to send customer notification: " . $ieblE->getMessage()); } } }