<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-25 10:05:36              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema; return new class extends Migration { public function up(): void { Schema::create('deposit_mutations', function (Blueprint $xZfQ4) { goto HMHK7; AeI5l: $xZfQ4->decimal('balance_after', 15, 2)->default(0); goto Lqt9L; trwIC: $xZfQ4->decimal('amount', 15, 2); goto jHbRa; mLoDP: $xZfQ4->enum('type', ['credit', 'debit']); goto trwIC; HMHK7: $xZfQ4->id(); goto gDvuW; gDvuW: $xZfQ4->foreignId('user_id')->constrained('users')->cascadeOnDelete(); goto mLoDP; pMlTy: $xZfQ4->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete(); goto RoySv; RoySv: $xZfQ4->timestamps(); goto wCqh2; jHbRa: $xZfQ4->decimal('balance_before', 15, 2)->default(0); goto AeI5l; Lqt9L: $xZfQ4->string('notes')->nullable(); goto pMlTy; wCqh2: }); } public function down(): void { Schema::dropIfExists('deposit_mutations'); } };