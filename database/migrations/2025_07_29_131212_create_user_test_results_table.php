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
        Schema::create('user_test_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->morphs('testable'); // Polymorphic relation (typing_tests or training_tests)
            $table->integer('score')->nullable();
            $table->decimal('accuracy', 5, 2)->nullable(); // For typing tests
            $table->integer('wpm')->nullable(); // Words per minute for typing tests
            $table->json('answers')->nullable(); // User's answers for training tests
            $table->boolean('passed')->default(false);
            $table->integer('time_taken_seconds')->nullable();
            $table->dateTime('completed_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_test_results');
    }
};
