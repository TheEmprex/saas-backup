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
        Schema::create('user_availability', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('day_of_week'); // monday, tuesday, etc.
            $table->time('start_time'); // Start time in user's timezone
            $table->time('end_time'); // End time in user's timezone
            $table->string('timezone', 50); // User's timezone (e.g., America/New_York)
            $table->boolean('is_available')->default(true);
            $table->timestamps();
            
            $table->index(['user_id', 'day_of_week']);
            $table->index(['user_id', 'is_available']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_availability');
    }
};
