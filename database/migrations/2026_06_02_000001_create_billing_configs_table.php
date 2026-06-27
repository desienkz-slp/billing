<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-26 22:34:38              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema; return new class extends Migration { public function up(): void { if (!Schema::hasTable('billing_configs')) { Schema::create('billing_configs', function (Blueprint $EUOFn) { goto TwGf3; bTjEe: $EUOFn->string('key', 100); goto r7zmC; GWQn3: $EUOFn->unique(['tenant_id', 'key']); goto uOxDx; TwGf3: $EUOFn->id(); goto Hs3Pq; r7zmC: $EUOFn->text('value')->nullable(); goto trpgK; trpgK: $EUOFn->timestamps(); goto GWQn3; uOxDx: $EUOFn->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade'); goto nCsoQ; Hs3Pq: $EUOFn->unsignedBigInteger('tenant_id'); goto bTjEe; nCsoQ: }); } } public function down(): void { Schema::dropIfExists('billing_configs'); } };
