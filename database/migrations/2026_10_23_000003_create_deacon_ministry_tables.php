<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('can_record_attendance')->default(false)->after('onboarding_completed_at');
        });

        Schema::create('deacon_member_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('deacon_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('member_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['deacon_id', 'member_id']);
        });

        Schema::create('deacon_visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('deacon_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('member_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('visited_on');
            $table->string('visit_type', 40);
            $table->text('notes');
            $table->date('follow_up_on')->nullable();
            $table->timestamps();
        });

        Schema::create('deacon_spiritual_follow_ups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('deacon_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('member_id')->constrained('users')->cascadeOnDelete();
            $table->string('status', 30)->default('open');
            $table->text('notes');
            $table->date('next_follow_up_on')->nullable();
            $table->timestamps();
        });

        Schema::create('deacon_assistance_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('deacon_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('member_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('category', 80);
            $table->text('details');
            $table->string('status', 30)->default('pending');
            $table->timestamps();
        });

        Schema::create('deacon_service_preparations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('deacon_id')->constrained('users')->cascadeOnDelete();
            $table->date('service_date');
            $table->string('task');
            $table->string('status', 30)->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('deacon_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('deacon_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('content');
            $table->date('report_date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deacon_reports');
        Schema::dropIfExists('deacon_service_preparations');
        Schema::dropIfExists('deacon_assistance_requests');
        Schema::dropIfExists('deacon_spiritual_follow_ups');
        Schema::dropIfExists('deacon_visits');
        Schema::dropIfExists('deacon_member_assignments');
        Schema::table('users', fn (Blueprint $table) => $table->dropColumn('can_record_attendance'));
    }
};
