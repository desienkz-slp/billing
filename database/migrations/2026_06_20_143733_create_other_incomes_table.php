<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-26 22:34:38              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema; return new class extends Migration { public function up(): void { Schema::create('other_incomes', function (Blueprint $EUOFn) { goto l9AEu; HiWXm: $EUOFn->integer('amount'); goto iV_a7; DRGJs: $EUOFn->timestamps(); goto rdykT; rdykT: $EUOFn->index(['tenant_id', 'income_date']); goto ZcD4V; yIAJP: $EUOFn->text('notes')->nullable(); goto DRGJs; sS7qq: $EUOFn->string('payment_method', 30)->default('cash'); goto yIAJP; l1swz: $EUOFn->foreignId('tenant_id')->constrained()->cascadeOnDelete(); goto N3xke; ZcD4V: $EUOFn->index(['tenant_id', 'category']); goto l8evA; l9AEu: $EUOFn->id(); goto l1swz; N3xke: $EUOFn->foreignId('customer_id')->nullable()->constrained()->nullOnDelete(); goto O1unj; iV_a7: $EUOFn->date('income_date'); goto sS7qq; Dvh40: $EUOFn->string('category', 50); goto HiWXm; O1unj: $EUOFn->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete(); goto Dvh40; l8evA: }); } public function down(): void { Schema::dropIfExists('other_incomes'); } };
