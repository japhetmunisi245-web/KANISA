<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            // Keep the original constraint in sync with the roles used by the
            // application. SQLite cannot alter an enum/check constraint in a
            // later migration, which previously made fresh test databases
            // reject administrators and church-office users.
            $table->enum('role', [
                'kiongozi',
                'mshirika',
                'admin',
                'mchungaji',
                'secretary',
                'deacon',
                'accountant',
            ])->default('mshirika')->after('phone');
            $table->string('cheo')->nullable()->after('role');
            $table->string('avatar')->nullable()->after('cheo');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'role', 'cheo', 'avatar']);
        });
    }
};
