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
            $table->unique('provider_external_id', 'photo_orders_provider_external_id_unique');
        });
        Schema::table('subscription_orders', function (Blueprint $table) {
            $table->unique('provider_external_id', 'subscription_orders_provider_external_id_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('photo_orders', function (Blueprint $table) {
            $table->dropUnique('photo_orders_provider_external_id_unique');
        });
        Schema::table('subscription_orders', function (Blueprint $table) {
            $table->dropUnique('subscription_orders_provider_external_id_unique');
        });
    }
};
