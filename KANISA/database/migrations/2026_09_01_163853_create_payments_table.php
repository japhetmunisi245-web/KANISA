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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 12, 2);
            $table->string('type'); // Sadaka, Fungu la Kumi, Ujenzi, Msaada, Ahadi, Michango, Nyingine
            $table->string('payment_method')->default('Cash'); // Cash, M-Pesa, Airtel Money, Tigo Pesa, HaloPesa
            $table->string('transaction_id')->nullable();
            $table->date('payment_date');
            $table->string('status')->default('approved'); // pending, approved, rejected
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
