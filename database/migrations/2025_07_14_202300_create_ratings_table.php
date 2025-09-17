<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('ratings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('rater_id')->constrained('users')->onDelete('cascade'); // Who is giving the rating
            $table->foreignId('rated_id')->constrained('users')->onDelete('cascade'); // Who is being rated
            $table->foreignId('job_post_id')->nullable()->constrained()->onDelete('cascade'); // Related job if applicable

            // Rating details
            $table->integer('overall_rating'); // 1-5 stars
            $table->integer('communication_rating')->nullable(); // 1-5 stars
            $table->integer('professionalism_rating')->nullable(); // 1-5 stars
            $table->integer('timeliness_rating')->nullable(); // 1-5 stars
            $table->integer('quality_rating')->nullable(); // 1-5 stars

            // Review content
            $table->text('review_title')->nullable();
            $table->text('review_content')->nullable();

            // Chatter-specific ratings
            $table->integer('conversion_rate_rating')->nullable(); // 1-5 stars
            $table->integer('response_time_rating')->nullable(); // 1-5 stars

            // Agency-specific ratings
            $table->integer('payment_reliability_rating')->nullable(); // 1-5 stars
            $table->integer('expectation_clarity_rating')->nullable(); // 1-5 stars

            // Metadata
            $table->boolean('is_verified')->default(false); // If the rating is from a verified job
            $table->boolean('is_public')->default(true);
            $table->json('metrics')->nullable(); // Additional performance metrics

            $table->timestamps();

            $table->unique(['rater_id', 'rated_id', 'job_post_id']); // Prevent duplicate ratings for same job
            $table->index(['rated_id', 'overall_rating']);
            $table->index(['is_verified', 'is_public']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};
