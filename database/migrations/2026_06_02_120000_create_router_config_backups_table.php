<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-26 22:34:38              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema; return new class extends Migration { public function up(): void { Schema::create('router_config_backups', function (Blueprint $EUOFn) { goto TmySd; TmySd: $EUOFn->id(); goto z3PQL; A3gMK: $EUOFn->text('config_text'); goto Bf5r6; vKcvh: $EUOFn->index(['router_id', 'created_at']); goto eVFfV; Bf5r6: $EUOFn->timestamps(); goto vKcvh; ys6BT: $EUOFn->string('config_name', 200); goto A3gMK; z3PQL: $EUOFn->foreignId('router_id')->constrained()->onDelete('cascade'); goto ys6BT; eVFfV: }); } public function down(): void { Schema::dropIfExists('router_config_backups'); } };
