<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('offerings', function (Blueprint $table) {
            $table->string('fund_scope')->default('internal')->after('offering_type');
        });
    }

    public function down(): void
    {
        Schema::table('offerings', function (Blueprint $table) {
            $table->dropColumn('fund_scope');
        });
    }
};
