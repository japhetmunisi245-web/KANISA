<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('joined_as_visitor')->default(false)->after('status');
            $table->date('joined_church_at')->nullable()->after('joined_as_visitor');
        });

        Schema::table('service_visitors', function (Blueprint $table) {
            $table->foreignId('member_id')->nullable()->after('user_id')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('service_visitors', function (Blueprint $table) {
            $table->dropConstrainedForeignId('member_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['joined_as_visitor', 'joined_church_at']);
        });
    }
};
