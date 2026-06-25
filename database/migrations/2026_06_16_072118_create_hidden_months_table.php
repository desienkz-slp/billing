<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-25 10:05:34              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema; return new class extends Migration { public function up(): void { Schema::create('hidden_months', function (Blueprint $ZVt4K) { goto qR7mt; f3llo: $ZVt4K->unique(['tenant_id', 'periode']); goto cEwkP; vg8jR: $ZVt4K->unsignedBigInteger('tenant_id'); goto fYJds; yjIZ5: $ZVt4K->timestamps(); goto f3llo; qR7mt: $ZVt4K->id(); goto vg8jR; fYJds: $ZVt4K->string('periode', 7); goto yjIZ5; cEwkP: }); } public function down(): void { Schema::dropIfExists('hidden_months'); } };