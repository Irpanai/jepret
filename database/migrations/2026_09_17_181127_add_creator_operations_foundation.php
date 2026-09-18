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
        Schema::create('platform_settings', function (Blueprint $table): void {
            $table->id();
            $table->string('key')->unique();
            $table->json('value');
            $table->timestamps();
        });
        Schema::create('admin_audit_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action')->index();
            $table->string('subject_type');
            $table->unsignedBigInteger('subject_id');
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->index(['subject_type', 'subject_id']);
        });
        Schema::table('users', function (Blueprint $table): void {
            $table->decimal('storage_quota_override_mb', 12, 2)->nullable();
            $table->boolean('photographer_watermark_locked')->default(false);
            $table->text('verification_rejection_reason')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->index(['role', 'is_verified']);
        });
        Schema::table('photos', function (Blueprint $table): void {
            $table->string('purchased_path')->nullable();
            $table->string('personal_watermark_path')->nullable();
            $table->string('media_type')->default('image');
            $table->unsignedBigInteger('storage_bytes')->nullable();
            $table->index(['fotografer_id', 'status', 'created_at']);
        });
        Schema::table('transactions', function (Blueprint $table): void {
            $table->unsignedInteger('payment_fee_amount')->nullable();
            $table->index(['fotografer_id', 'status', 'paid_at']);
        });
        Schema::table('withdrawals', function (Blueprint $table): void {
            $table->string('status')->default('pending')->change();
            $table->uuid('idempotency_key')->nullable();
            $table->string('destination_type')->default('bank');
            $table->string('account_holder')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->unique(['fotografer_id', 'idempotency_key']);
            $table->index(['fotografer_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        throw new RuntimeException('This additive operational migration requires a forward fix; rollback could discard financial review history.');
    }
};
