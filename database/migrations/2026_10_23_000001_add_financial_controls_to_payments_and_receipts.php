<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->foreignId('recorded_by')->nullable()->after('user_id')->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->after('recorded_by')->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable()->after('approved_by');
            $table->text('rejection_reason')->nullable()->after('approved_at');
            $table->unique('transaction_id', 'payments_transaction_id_unique');
        });

        Schema::table('receipts', function (Blueprint $table) {
            $table->unique('payment_id', 'receipts_payment_id_unique');
        });
    }

    public function down(): void
    {
        Schema::table('receipts', function (Blueprint $table) {
            $table->dropUnique('receipts_payment_id_unique');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropUnique('payments_transaction_id_unique');
            $table->dropConstrainedForeignId('approved_by');
            $table->dropConstrainedForeignId('recorded_by');
            $table->dropColumn(['approved_at', 'rejection_reason']);
        });
    }
};
