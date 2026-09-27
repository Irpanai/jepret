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
        Schema::table('photos', function (Blueprint $table) {
            $table->unsignedBigInteger('original_bytes')->nullable()->after('storage_bytes');
            $table->unsignedBigInteger('preview_bytes')->nullable()->after('original_bytes');
            $table->unsignedBigInteger('purchased_bytes')->nullable()->after('preview_bytes');
        });

        DB::table('photos')->whereNotNull('storage_bytes')->update(['original_bytes' => DB::raw('storage_bytes'), 'preview_bytes' => 0, 'purchased_bytes' => 0]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('photos', function (Blueprint $table) {
            $table->dropColumn(['original_bytes', 'preview_bytes', 'purchased_bytes']);
        });
    }
};
