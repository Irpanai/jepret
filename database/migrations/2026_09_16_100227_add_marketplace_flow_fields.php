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
        Schema::table('users', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('name');
            $table->string('studio_name')->nullable()->after('avatar');
            $table->string('whatsapp')->nullable()->after('studio_name');
            $table->string('location')->nullable()->after('whatsapp');
            $table->string('category')->nullable()->after('location');
            $table->text('bio')->nullable()->after('category');
            $table->string('profile_photo_path')->nullable()->after('bio');
            $table->timestamp('photographer_onboarded_at')->nullable()->after('profile_photo_path');
            $table->string('bank_name')->nullable()->after('custom_watermark_path');
            $table->string('bank_account_name')->nullable()->after('bank_name');
            $table->string('bank_account_number')->nullable()->after('bank_account_name');
        });

        Schema::table('packages', function (Blueprint $table) {
            $table->string('code')->nullable()->unique()->after('id');
            $table->string('display_name')->nullable()->after('nama_paket');
            $table->string('billing_period')->nullable()->after('harga');
            $table->boolean('is_custom')->default(false)->after('billing_period');
        });

        Schema::table('photos', function (Blueprint $table) {
            $table->string('title')->nullable()->after('fotografer_id');
            $table->string('status')->default('active')->after('harga')->index();
            $table->timestamp('published_at')->nullable()->after('status');
            $table->timestamp('taken_at')->nullable()->after('published_at');
            $table->string('daypart')->nullable()->after('taken_at');
            $table->string('category')->nullable()->after('daypart');
            $table->string('ratio')->nullable()->after('category');
            $table->unsignedBigInteger('views_count')->default(0)->after('ratio');
            $table->string('original_filename')->nullable()->after('views_count');
            $table->decimal('file_size_mb', 10, 2)->nullable()->after('original_filename');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->string('order_number')->nullable()->index()->after('id');
            $table->foreignId('fotografer_id')->nullable()->after('photo_id')->constrained('users')->nullOnDelete();
            $table->string('payment_status')->default('pending')->index()->after('status');
            $table->integer('photographer_amount')->default(0)->after('total_bayar');
            $table->integer('platform_amount')->default(0)->after('photographer_amount');
            $table->json('revenue_share_snapshot')->nullable()->after('platform_amount');
            $table->string('payment_method')->nullable()->after('payment_status');
            $table->string('payment_reference')->nullable()->after('payment_method');
            $table->timestamp('paid_at')->nullable()->after('payment_reference');
            $table->timestamp('expires_at')->nullable()->after('paid_at');
        });

        Schema::table('withdrawals', function (Blueprint $table) {
            $table->string('review_status')->default('pending')->index()->after('status');
            $table->text('admin_notes')->nullable()->after('review_status');
            $table->timestamp('processed_at')->nullable()->after('admin_notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('withdrawals', function (Blueprint $table) {
            $table->dropColumn(['review_status', 'admin_notes', 'processed_at']);
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['fotografer_id']);
            $table->dropColumn([
                'order_number',
                'fotografer_id',
                'payment_status',
                'photographer_amount',
                'platform_amount',
                'revenue_share_snapshot',
                'payment_method',
                'payment_reference',
                'paid_at',
                'expires_at',
            ]);
        });

        Schema::table('photos', function (Blueprint $table) {
            $table->dropColumn([
                'title',
                'status',
                'published_at',
                'taken_at',
                'daypart',
                'category',
                'ratio',
                'views_count',
                'original_filename',
                'file_size_mb',
            ]);
        });

        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn(['code', 'display_name', 'billing_period', 'is_custom']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'slug',
                'studio_name',
                'whatsapp',
                'location',
                'category',
                'bio',
                'profile_photo_path',
                'photographer_onboarded_at',
                'bank_name',
                'bank_account_name',
                'bank_account_number',
            ]);
        });
    }
};
