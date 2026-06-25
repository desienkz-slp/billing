<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-25 10:05:33              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema; return new class extends Migration { public function up(): void { Schema::create('router_config_backups', function (Blueprint $XgBca) { goto QVeqL; Vqv5x: $XgBca->index(['router_id', 'created_at']); goto BkJFT; QVeqL: $XgBca->id(); goto QwQ4z; vPjsM: $XgBca->timestamps(); goto Vqv5x; Y2zRo: $XgBca->string('config_name', 200); goto nWBE_; QwQ4z: $XgBca->foreignId('router_id')->constrained()->onDelete('cascade'); goto Y2zRo; nWBE_: $XgBca->text('config_text'); goto vPjsM; BkJFT: }); } public function down(): void { Schema::dropIfExists('router_config_backups'); } };