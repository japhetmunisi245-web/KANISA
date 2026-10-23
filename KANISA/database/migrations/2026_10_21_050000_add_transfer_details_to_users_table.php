<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('origin_location')->nullable()->after('joined_church_at');
            $table->string('current_location')->nullable()->after('origin_location');
            $table->string('previous_church_name')->nullable()->after('current_location');
            $table->string('previous_church_location')->nullable()->after('previous_church_name');
            $table->string('previous_pastor_name')->nullable()->after('previous_church_location');
            $table->string('previous_pastor_phone', 30)->nullable()->after('previous_pastor_name');
            $table->string('introduction_letter_path')->nullable()->after('previous_pastor_phone');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'origin_location', 'current_location', 'previous_church_name',
                'previous_church_location', 'previous_pastor_name', 'previous_pastor_phone',
                'introduction_letter_path',
            ]);
        });
    }
};
