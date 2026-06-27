<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-26 22:34:38              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema; return new class extends Migration { public function up(): void { Schema::create('hidden_months', function (Blueprint $EUOFn) { goto PGSe2; bHSOD: $EUOFn->unique(['tenant_id', 'periode']); goto zEqFG; mjdEE: $EUOFn->unsignedBigInteger('tenant_id'); goto Of6uD; Of6uD: $EUOFn->string('periode', 7); goto cpgZi; cpgZi: $EUOFn->timestamps(); goto bHSOD; PGSe2: $EUOFn->id(); goto mjdEE; zEqFG: }); } public function down(): void { Schema::dropIfExists('hidden_months'); } };
