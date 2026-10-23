<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('street_name')->nullable()->after('address');
            $table->string('ward')->nullable()->after('street_name');
            $table->string('marital_status')->nullable()->after('ward');
            $table->string('spouse_names')->nullable()->after('marital_status');
            $table->unsignedTinyInteger('children_count')->nullable()->after('spouse_names');
            $table->date('saved_date')->nullable()->after('children_count');
            $table->date('baptism_date')->nullable()->after('saved_date');
            $table->string('education')->nullable()->after('baptism_date');
            $table->string('service')->nullable()->after('education');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['street_name', 'ward', 'marital_status', 'spouse_names', 'children_count', 'saved_date', 'baptism_date', 'education', 'service']);
        });
    }
};
