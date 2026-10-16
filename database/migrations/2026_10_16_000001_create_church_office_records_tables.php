<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('meeting_minutes', function (Blueprint $table) {
            $table->id(); $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('meeting_type'); $table->date('meeting_date'); $table->string('title');
            $table->text('minutes'); $table->timestamps();
        });
        Schema::create('duty_schedules', function (Blueprint $table) {
            $table->id(); $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('week_start'); $table->string('service_name'); $table->string('person_name');
            $table->string('duty'); $table->text('notes')->nullable(); $table->timestamps();
            $table->unique(['week_start', 'service_name', 'person_name', 'duty']);
        });
        Schema::create('service_attendances', function (Blueprint $table) {
            $table->id(); $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('service_date'); $table->string('service_name');
            $table->unsignedInteger('male_count')->default(0); $table->unsignedInteger('female_count')->default(0);
            $table->unsignedInteger('children_count')->default(0); $table->text('notes')->nullable(); $table->timestamps();
        });
        Schema::create('offerings', function (Blueprint $table) {
            $table->id(); $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('offering_date'); $table->string('offering_type')->default('Sadaka');
            $table->decimal('amount', 12, 2); $table->string('payment_method')->default('Cash');
            $table->text('notes')->nullable(); $table->timestamps();
        });
        Schema::create('church_announcements', function (Blueprint $table) {
            $table->id(); $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('audience')->default('all'); $table->string('title'); $table->text('content');
            $table->date('expires_at')->nullable(); $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('church_announcements'); Schema::dropIfExists('offerings'); Schema::dropIfExists('service_attendances'); Schema::dropIfExists('duty_schedules'); Schema::dropIfExists('meeting_minutes'); }
};
