<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-25 10:05:33              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema; return new class extends Migration { public function up(): void { if (!Schema::hasTable('billing_configs')) { Schema::create('billing_configs', function (Blueprint $a19Nk) { goto yNSZV; XhQa0: $a19Nk->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade'); goto HSRDe; qRZmK: $a19Nk->unique(['tenant_id', 'key']); goto XhQa0; fHgfs: $a19Nk->string('key', 100); goto TZD94; TZD94: $a19Nk->text('value')->nullable(); goto tjXPH; yNSZV: $a19Nk->id(); goto s4IT9; s4IT9: $a19Nk->unsignedBigInteger('tenant_id'); goto fHgfs; tjXPH: $a19Nk->timestamps(); goto qRZmK; HSRDe: }); } } public function down(): void { Schema::dropIfExists('billing_configs'); } };