<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-25 10:05:34              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema; return new class extends Migration { public function up(): void { Schema::create('db_pusat_profiles', function (Blueprint $Jsz20) { goto SQRfj; fTM6V: $Jsz20->timestamps(); goto N3GLe; ru0af: $Jsz20->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade'); goto P0kOH; uygvo: $Jsz20->string('description')->nullable(); goto fTM6V; N3GLe: $Jsz20->unique(['tenant_id', 'name']); goto ru0af; jc9uW: $Jsz20->unsignedBigInteger('tenant_id'); goto bfODL; bfODL: $Jsz20->string('name', 150); goto uygvo; SQRfj: $Jsz20->id(); goto jc9uW; P0kOH: }); } public function down(): void { Schema::dropIfExists('db_pusat_profiles'); } };