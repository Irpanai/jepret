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
        Schema::create('cameras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fotografer_id')->constrained('users')->cascadeOnDelete();
            $table->string('name');
            $table->string('brand_model');
            $table->string('lens')->nullable();
            $table->string('photo_path')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::table('photos', function (Blueprint $table) {
            $table->foreignId('camera_id')->nullable()->after('fotografer_id')->constrained('cameras')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('photos', function (Blueprint $table) {
            $table->dropForeign(['camera_id']);
            $table->dropColumn('camera_id');
        });

        Schema::dropIfExists('cameras');
    }
};
