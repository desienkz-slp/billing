<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-25 10:05:36              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema; return new class extends Migration { public function up(): void { Schema::create('package_servers', function (Blueprint $jdvy6) { goto Ym5Oc; LiyD5: $jdvy6->unique(['tenant_id', 'package_id', 'server_id']); goto P6oh3; p02Zd: $jdvy6->timestamps(); goto LiyD5; Y4o6T: $jdvy6->foreignId('tenant_id')->constrained()->cascadeOnDelete(); goto u8Iv7; Ym5Oc: $jdvy6->id(); goto Y4o6T; u8Iv7: $jdvy6->foreignId('package_id')->constrained()->cascadeOnDelete(); goto Aujpo; Aujpo: $jdvy6->foreignId('server_id')->constrained()->cascadeOnDelete(); goto GanQ1; IQK0N: $jdvy6->string('radius_isolir_group', 100)->nullable(); goto p02Zd; GanQ1: $jdvy6->string('radius_group', 100)->nullable(); goto IQK0N; P6oh3: }); } public function down(): void { Schema::dropIfExists('package_servers'); } };