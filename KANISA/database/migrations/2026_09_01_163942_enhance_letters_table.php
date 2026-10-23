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
        Schema::table('letters', function (Blueprint $table) {
            $table->foreignId('department_id')->nullable()->change();
            $table->string('letter_type')->nullable()->after('user_id');
            $table->string('reference_number')->unique()->nullable()->after('letter_type');
            $table->string('recipient')->nullable()->after('subject');
            $table->enum('status', ['draft', 'pending', 'under_review', 'approved', 'rejected', 'generated', 'downloaded'])->default('pending')->after('body');
            $table->text('rejection_reason')->nullable()->after('status');
            $table->string('pdf_path')->nullable()->after('rejection_reason');
            $table->string('verification_token')->unique()->nullable()->after('pdf_path');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('letters', function (Blueprint $table) {
            $table->dropColumn([
                'letter_type', 'reference_number', 'recipient', 'status',
                'rejection_reason', 'pdf_path', 'verification_token',
                'approved_by', 'approved_at'
            ]);
        });
    }
};
