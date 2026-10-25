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
        Schema::table('photos', function (Blueprint $table): void {
            $table->char('original_sha256', 64)->nullable()->after('original_filename');
        });
        Schema::table('transactions', function (Blueprint $table): void {
            $table->char('download_sha256', 64)->nullable()->after('download_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table): void {
            $table->dropColumn('download_sha256');
        });
        Schema::table('photos', function (Blueprint $table): void {
            $table->dropColumn('original_sha256');
        });
    }
};
