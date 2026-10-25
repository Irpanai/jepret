<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('platform_settings')->updateOrInsert(
            ['key' => 'minimum_withdrawal'],
            ['value' => json_encode(25000), 'updated_at' => now(), 'created_at' => now()],
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('platform_settings')
            ->where('key', 'minimum_withdrawal')
            ->update(['value' => json_encode(100000), 'updated_at' => now()]);
    }
};
