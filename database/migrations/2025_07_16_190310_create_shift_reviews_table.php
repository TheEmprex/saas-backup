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
        Schema::create('shift_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_shift_id')->constrained()->onDelete('cascade');
            $table->foreignId('employment_contract_id')->constrained()->onDelete('cascade');
            $table->foreignId('reviewer_id')->constrained('users')->onDelete('cascade'); // Agency reviewing
            $table->foreignId('chatter_id')->constrained('users')->onDelete('cascade'); // Chatter being reviewed
            
            // Review ratings (1-5 stars)
            $table->integer('overall_rating'); // 1-5
            $table->integer('communication_rating')->nullable();
            $table->integer('reliability_rating')->nullable();
            $table->integer('quality_rating')->nullable();
            $table->integer('professionalism_rating')->nullable();
            
            // Review details
            $table->text('review_comment')->nullable();
            $table->text('positive_feedback')->nullable();
            $table->text('areas_for_improvement')->nullable();
            
            // Performance flags
            $table->boolean('on_time')->default(true);
            $table->boolean('completed_tasks')->default(true);
            $table->boolean('followed_instructions')->default(true);
            $table->boolean('professional_behavior')->default(true);
            
            // Would recommend flags
            $table->boolean('would_hire_again')->default(true);
            $table->boolean('recommend_to_others')->default(true);
            
            $table->timestamps();
            
            $table->unique(['work_shift_id']); // One review per shift
            $table->index(['employment_contract_id', 'overall_rating']);
            $table->index(['chatter_id', 'overall_rating']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shift_reviews');
    }
};
