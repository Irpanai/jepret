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
        Schema::table('packages', function (Blueprint $table) {
            $table->string('nama_paket')->default('Basic')->change();
            $table->text('description')->nullable()->after('display_name');
            $table->json('features')->nullable()->after('description');
            $table->char('currency', 3)->default('IDR')->after('harga');
            $table->unsignedSmallInteger('duration_days')->nullable()->after('currency');
            $table->unsignedBigInteger('storage_quota_bytes')->nullable()->after('kuota_storage_mb');
            $table->boolean('is_active')->default(true)->after('is_custom');
            $table->boolean('is_trial')->default(false)->after('is_active');
            $table->boolean('is_legacy')->default(false)->after('is_trial');
            $table->unsignedSmallInteger('sort_order')->default(0)->after('is_legacy');
            $table->unsignedInteger('revision')->default(1)->after('sort_order');

            $table->index(['is_active', 'is_legacy', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        throw new RuntimeException('Package catalog expansion is forward-only because reverting nama_paket to the legacy enum would discard valid package names.');
    }
};
