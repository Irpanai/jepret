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
        foreach (['photo_orders', 'subscription_orders'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table): void {
                $table->unsignedBigInteger('original_subtotal')->nullable();
                $table->foreignId('voucher_id')->nullable()->constrained()->nullOnDelete();
                $table->string('voucher_code')->nullable();
                $table->unsignedBigInteger('discount_amount')->default(0);
                $table->unsignedBigInteger('final_payable')->nullable();
            });
        }
        Schema::table('photo_orders', function (Blueprint $table): void {
            $table->unsignedBigInteger('photographer_amount')->nullable();
            $table->unsignedBigInteger('platform_commission')->nullable();
            $table->unsignedBigInteger('platform_revenue')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('photo_orders', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('voucher_id');
            $table->dropColumn(['original_subtotal', 'voucher_code', 'discount_amount', 'final_payable', 'photographer_amount', 'platform_commission', 'platform_revenue']);
        });
        Schema::table('subscription_orders', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('voucher_id');
            $table->dropColumn(['original_subtotal', 'voucher_code', 'discount_amount', 'final_payable']);
        });
    }
};
