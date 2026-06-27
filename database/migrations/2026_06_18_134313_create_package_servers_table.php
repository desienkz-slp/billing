<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-26 22:34:38              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema; return new class extends Migration { public function up(): void { Schema::create('package_servers', function (Blueprint $EUOFn) { goto JZiIq; JZiIq: $EUOFn->id(); goto sixb1; ctgpE: $EUOFn->timestamps(); goto W2AbU; W2AbU: $EUOFn->unique(['tenant_id', 'package_id', 'server_id']); goto Pzdnf; kzK6l: $EUOFn->string('radius_group', 100)->nullable(); goto bydxj; bydxj: $EUOFn->string('radius_isolir_group', 100)->nullable(); goto ctgpE; Toivk: $EUOFn->foreignId('server_id')->constrained()->cascadeOnDelete(); goto kzK6l; r0jMi: $EUOFn->foreignId('package_id')->constrained()->cascadeOnDelete(); goto Toivk; sixb1: $EUOFn->foreignId('tenant_id')->constrained()->cascadeOnDelete(); goto r0jMi; Pzdnf: }); } public function down(): void { Schema::dropIfExists('package_servers'); } };
