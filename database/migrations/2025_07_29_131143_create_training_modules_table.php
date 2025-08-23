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
        Schema::create('training_modules', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('video_url')->nullable(); // YouTube or local video URL
            $table->text('content')->nullable(); // Text content/instructions
            $table->integer('duration_minutes')->nullable(); // Estimated completion time
            $table->integer('order')->default(0); // Order in curriculum
            $table->boolean('is_active')->default(true);
            $table->json('prerequisites')->nullable(); // IDs of required modules
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_modules');
    }
};
