<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('package_id')->nullable()->constrained('packages')->nullOnDelete();
            $table->string('status')->default('active')->index();
            $table->json('entitlement_snapshot');
            $table->timestamp('starts_at');
            $table->timestamp('ends_at')->nullable()->index();
            $table->timestamp('trial_used_at')->nullable();
            $table->timestamp('last_payment_at')->nullable();
            $table->string('last_order_id')->nullable();
            $table->unsignedInteger('version')->default(1);
            $table->boolean('is_legacy_transition')->default(false);
            $table->timestamps();
        });

        Schema::create('subscription_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_id')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('package_id')->nullable()->constrained('packages')->nullOnDelete();
            $table->string('intent')->index();
            $table->json('package_snapshot');
            $table->unsignedBigInteger('gross_amount');
            $table->char('currency', 3)->default('IDR');
            $table->string('provider')->default('midtrans');
            $table->string('status')->default('pending')->index();
            $table->string('provider_status')->nullable();
            $table->string('provider_transaction_id')->nullable()->unique();
            $table->text('snap_token')->nullable();
            $table->text('snap_redirect_url')->nullable();
            $table->unsignedInteger('source_subscription_version')->default(0);
            $table->boolean('requires_review')->default(false);
            $table->timestamp('provider_paid_at')->nullable()->index();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'status', 'created_at']);
        });

        Schema::create('payment_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_order_id')->nullable()->constrained()->nullOnDelete();
            $table->string('provider')->default('midtrans');
            $table->string('event_key')->unique();
            $table->string('provider_transaction_id')->nullable()->index();
            $table->string('provider_status')->nullable();
            $table->unsignedBigInteger('gross_amount')->nullable();
            $table->boolean('signature_valid')->default(false);
            $table->json('payload')->nullable();
            $table->string('processing_result')->nullable();
            $table->timestamp('provider_event_at')->nullable();
            $table->timestamps();
        });

        Schema::create('subscription_refunds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_order_id')->constrained()->restrictOnDelete();
            $table->foreignId('requested_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedBigInteger('amount');
            $table->text('reason');
            $table->string('status')->default('pending')->index();
            $table->text('review_notes')->nullable();
            $table->string('provider_reference')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });

        $activatedAt = now();
        DB::table('users')->where('role', 'fotografer')->orderBy('id')->each(function (object $user) use ($activatedAt): void {
            $package = $user->package_id ? DB::table('packages')->find($user->package_id) : null;
            $quotaBytes = $package?->storage_quota_bytes ?? ((int) ($package?->kuota_storage_mb ?? 5000) * 1048576);
            DB::table('subscriptions')->insert([
                'user_id' => $user->id,
                'package_id' => $user->package_id,
                'status' => 'active',
                'entitlement_snapshot' => json_encode(['code' => $package?->code ?? 'legacy', 'name' => $package?->display_name ?? $package?->nama_paket ?? 'Legacy', 'storage_quota_bytes' => $quotaBytes, 'duration_days' => 30, 'revision' => $package?->revision ?? 1], JSON_THROW_ON_ERROR),
                'starts_at' => $activatedAt,
                'ends_at' => $activatedAt->copy()->addDays(30),
                'version' => 1,
                'is_legacy_transition' => true,
                'created_at' => $activatedAt,
                'updated_at' => $activatedAt,
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_refunds');
        Schema::dropIfExists('payment_events');
        Schema::dropIfExists('subscription_orders');
        Schema::dropIfExists('subscriptions');
    }
};
