<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('church_announcements', function (Blueprint $table) {
            $table->string('media_path')->nullable()->after('content');
            $table->string('media_type', 30)->nullable()->after('media_path');
            $table->string('media_original_name')->nullable()->after('media_type');
        });
    }

    public function down(): void
    {
        Schema::table('church_announcements', function (Blueprint $table) {
            $table->dropColumn(['media_path', 'media_type', 'media_original_name']);
        });
    }
};
