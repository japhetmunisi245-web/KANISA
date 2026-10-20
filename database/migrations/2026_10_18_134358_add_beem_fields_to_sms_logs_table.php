<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sms_logs', function (Blueprint $table) {
            $table->unsignedInteger('sent_count')->default(0)->after('recipients_count');
            $table->unsignedInteger('failed_count')->default(0)->after('sent_count');
            $table->json('api_response')->nullable()->after('status');
            $table->text('error_message')->nullable()->after('api_response');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sms_logs', function (Blueprint $table) {
            $table->dropColumn(['sent_count', 'failed_count', 'api_response', 'error_message']);
        });
    }
};
