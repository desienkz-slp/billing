<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-25 10:05:37              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema; return new class extends Migration { public function up(): void { Schema::table('servers', function (Blueprint $XW0EX) { $XW0EX->text('api_token')->nullable()->change(); $XW0EX->text('password')->nullable()->change(); }); } public function down(): void { Schema::table('servers', function (Blueprint $XW0EX) { $XW0EX->string('api_token', 255)->nullable()->change(); $XW0EX->string('password', 255)->nullable()->change(); }); } };