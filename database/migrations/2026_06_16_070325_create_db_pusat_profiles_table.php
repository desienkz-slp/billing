<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-26 22:34:38              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema; return new class extends Migration { public function up(): void { Schema::create('db_pusat_profiles', function (Blueprint $EUOFn) { goto lihDU; wQgJb: $EUOFn->unsignedBigInteger('tenant_id'); goto MFFzD; ouS4a: $EUOFn->timestamps(); goto h41Fq; WfA80: $EUOFn->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade'); goto j631w; pKion: $EUOFn->string('description')->nullable(); goto ouS4a; lihDU: $EUOFn->id(); goto wQgJb; h41Fq: $EUOFn->unique(['tenant_id', 'name']); goto WfA80; MFFzD: $EUOFn->string('name', 150); goto pKion; j631w: }); } public function down(): void { Schema::dropIfExists('db_pusat_profiles'); } };
