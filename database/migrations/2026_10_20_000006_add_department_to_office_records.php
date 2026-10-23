<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('meeting_minutes', function (Blueprint $table) {
            $table->foreignId('department_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
        });

        Schema::table('duty_schedules', function (Blueprint $table) {
            $table->foreignId('department_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('meeting_minutes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('department_id');
        });

        Schema::table('duty_schedules', function (Blueprint $table) {
            $table->dropConstrainedForeignId('department_id');
        });
    }
};
