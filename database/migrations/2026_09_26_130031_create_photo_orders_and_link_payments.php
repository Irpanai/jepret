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
        Schema::create('photo_orders', function (Blueprint $table): void {
            $table->id();
            $table->string('order_id')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedBigInteger('gross_amount');
            $table->char('currency', 3)->default('IDR');
            $table->string('provider')->default('midtrans');
            $table->string('status')->default('pending')->index();
            $table->string('provider_status')->nullable();
            $table->string('provider_transaction_id')->nullable()->unique();
            $table->string('payment_method')->nullable();
            $table->text('snap_token')->nullable();
            $table->text('snap_redirect_url')->nullable();
            $table->boolean('requires_review')->default(false);
            $table->timestamp('provider_paid_at')->nullable()->index();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'status', 'created_at']);
        });

        Schema::table('transactions', function (Blueprint $table): void {
            $table->foreignId('photo_order_id')->nullable()->after('order_number')->constrained()->restrictOnDelete();
        });

        Schema::table('payment_events', function (Blueprint $table): void {
            $table->foreignId('photo_order_id')->nullable()->after('subscription_order_id')->constrained()->nullOnDelete();
        });

        DB::table('transactions')->whereNotNull('order_number')->select('order_number')->distinct()->orderBy('order_number')
            ->each(function (object $group): void {
                $items = DB::table('transactions')->where('order_number', $group->order_number)->orderBy('id')->get();
                $paid = $items->every(fn (object $item): bool => $item->payment_status === 'paid');
                $failedStatus = $items->pluck('payment_status')->first(fn (?string $status): bool => in_array($status, ['failed', 'cancelled', 'expired'], true));
                $paidAt = $items->pluck('paid_at')->filter()->sortDesc()->first();
                $createdAt = $items->pluck('created_at')->filter()->sort()->first() ?? now();
                $updatedAt = $items->pluck('updated_at')->filter()->sortDesc()->first() ?? $createdAt;

                $photoOrderId = DB::table('photo_orders')->insertGetId([
                    'order_id' => $group->order_number,
                    'user_id' => $items->first()->pembeli_id,
                    'gross_amount' => $items->sum('total_bayar'),
                    'currency' => 'IDR',
                    'provider' => str_starts_with((string) $items->first()->payment_reference, 'LOCAL-') ? 'local' : 'legacy',
                    'status' => $paid ? 'paid' : ($failedStatus ?: 'pending'),
                    'provider_status' => $paid ? 'settlement' : null,
                    'provider_transaction_id' => null,
                    'payment_method' => $items->first()->payment_method,
                    'requires_review' => false,
                    'provider_paid_at' => $paidAt,
                    'expires_at' => $items->pluck('expires_at')->filter()->sortDesc()->first(),
                    'processed_at' => $paid ? ($paidAt ?: $updatedAt) : null,
                    'created_at' => $createdAt,
                    'updated_at' => $updatedAt,
                ]);

                DB::table('transactions')->where('order_number', $group->order_number)->update(['photo_order_id' => $photoOrderId]);
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        throw new RuntimeException('This financial migration requires a forward fix; rollback could detach historical payment records.');
    }
};
