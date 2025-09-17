<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('job_posts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Agency posting the job
            $table->string('title');
            $table->text('description');
            $table->json('requirements'); // JSON field for requirements

            // Job specifications
            $table->integer('min_typing_speed')->nullable();
            $table->integer('min_english_proficiency')->nullable();
            $table->json('required_traffic_sources')->nullable(); // Array of required traffic sources
            $table->string('market')->default('english'); // english, spanish, etc.
            $table->enum('experience_level', ['beginner', 'intermediate', 'advanced'])->default('beginner');
            $table->string('expected_response_time')->nullable(); // e.g., "within 5 minutes"

            // Compensation
            $table->decimal('hourly_rate', 8, 2)->nullable();
            $table->decimal('fixed_rate', 8, 2)->nullable();
            $table->enum('rate_type', ['hourly', 'fixed', 'commission'])->default('hourly');
            $table->decimal('commission_percentage', 5, 2)->nullable();

            // Job details
            $table->integer('hours_per_week')->nullable();
            $table->string('timezone_preference')->nullable();
            $table->json('working_hours')->nullable(); // Preferred working hours
            $table->enum('contract_type', ['full_time', 'part_time', 'project_based'])->default('part_time');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

            // Status and visibility
            $table->enum('status', ['draft', 'active', 'paused', 'closed', 'filled'])->default('draft');
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_urgent')->default(false);
            $table->integer('max_applications')->default(50);
            $table->integer('current_applications')->default(0);

            // Metadata
            $table->timestamp('expires_at')->nullable();
            $table->json('tags')->nullable(); // Array of tags for filtering
            $table->integer('views')->default(0);

            $table->timestamps();

            $table->index(['status', 'is_featured']);
            $table->index(['user_id', 'status']);
            $table->index(['created_at', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_posts');
    }
};
