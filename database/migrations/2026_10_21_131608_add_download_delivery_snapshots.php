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
        Schema::table('photos', function (Blueprint $table): void {
            $table->boolean('sell_without_watermark')->default(false)->after('purchased_path');
        });
        Schema::table('transactions', function (Blueprint $table): void {
            $table->string('download_path')->nullable()->after('photo_id');
            $table->boolean('download_without_watermark')->default(false)->after('download_path');
        });

        DB::table('transactions')->select(['id', 'photo_id'])->orderBy('id')->chunkById(500, function ($transactions): void {
            $paths = DB::table('photos')->whereIn('id', $transactions->pluck('photo_id'))->pluck('purchased_path', 'id');
            foreach ($transactions as $transaction) {
                DB::table('transactions')->where('id', $transaction->id)->update([
                    'download_path' => $paths[$transaction->photo_id] ?? null,
                    'download_without_watermark' => false,
                ]);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table): void {
            $table->dropColumn(['download_path', 'download_without_watermark']);
        });
        Schema::table('photos', function (Blueprint $table): void {
            $table->dropColumn('sell_without_watermark');
        });
    }
};
