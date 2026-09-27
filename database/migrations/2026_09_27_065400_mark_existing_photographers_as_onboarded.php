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
        DB::table('users')
            ->where('role', 'fotografer')
            ->whereNull('photographer_onboarded_at')
            ->update(['photographer_onboarded_at' => now()]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Existing Photographer accounts must not be forced through new onboarding.
    }
};
