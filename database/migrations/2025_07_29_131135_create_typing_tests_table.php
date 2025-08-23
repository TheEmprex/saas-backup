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
        Schema::create('typing_tests', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content'); // The text to be typed
            $table->integer('difficulty_level')->default(1); // 1-5 difficulty scale
            $table->integer('time_limit_seconds')->nullable(); // Optional time limit
            $table->integer('min_wpm')->nullable(); // Minimum WPM to pass
            $table->decimal('min_accuracy', 5, 2)->default(85.00); // Minimum accuracy percentage
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('typing_tests');
    }
};
