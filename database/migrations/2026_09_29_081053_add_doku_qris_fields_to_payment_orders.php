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
        Schema::table('photo_orders', function (Blueprint $table) {
            $table->text('qr_content')->nullable()->after('snap_redirect_url');
            $table->string('provider_external_id', 64)->nullable()->after('provider_transaction_id');
        });
        Schema::table('subscription_orders', function (Blueprint $table) {
            $table->text('qr_content')->nullable()->after('snap_redirect_url');
            $table->string('provider_external_id', 64)->nullable()->after('provider_transaction_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('photo_orders', function (Blueprint $table) {
            $table->dropColumn(['qr_content', 'provider_external_id']);
        });
        Schema::table('subscription_orders', function (Blueprint $table) {
            $table->dropColumn(['qr_content', 'provider_external_id']);
        });
    }
};
