<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-26 22:34:38              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema; return new class extends Migration { public function up(): void { Schema::create('deposit_mutations', function (Blueprint $EUOFn) { goto PA7zP; AzPWD: $EUOFn->foreignId('user_id')->constrained('users')->cascadeOnDelete(); goto cu9K8; MPKyW: $EUOFn->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete(); goto fyrVf; icNjS: $EUOFn->decimal('balance_before', 15, 2)->default(0); goto S9Cko; cu9K8: $EUOFn->enum('type', ['credit', 'debit']); goto opNkQ; HJvA6: $EUOFn->string('notes')->nullable(); goto MPKyW; PA7zP: $EUOFn->id(); goto AzPWD; S9Cko: $EUOFn->decimal('balance_after', 15, 2)->default(0); goto HJvA6; opNkQ: $EUOFn->decimal('amount', 15, 2); goto icNjS; fyrVf: $EUOFn->timestamps(); goto QcRUd; QcRUd: }); } public function down(): void { Schema::dropIfExists('deposit_mutations'); } };
