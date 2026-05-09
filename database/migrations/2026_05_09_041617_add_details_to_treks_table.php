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
        Schema::table('treks', function (Blueprint $table) {
            $table->decimal('price', 8, 2)->nullable();
            $table->integer('duration_days')->nullable();
            $table->enum('difficulty', ['easy', 'moderate', 'difficult'])->nullable();
            $table->string('location')->nullable();
            $table->string('image_url')->nullable();
            $table->integer('max_altitude')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('treks', function (Blueprint $table) {
            $table->dropColumn(['price', 'duration_days', 'difficulty', 'location', 'image_url', 'max_altitude']);
        });
    }
};
