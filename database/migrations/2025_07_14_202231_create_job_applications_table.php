<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('job_applications', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('job_post_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Chatter applying

            $table->text('cover_letter')->nullable();
            $table->decimal('proposed_rate', 8, 2)->nullable();
            $table->integer('available_hours')->nullable();
            $table->json('attachments')->nullable(); // Array of file paths
            $table->text('additional_notes')->nullable();

            // Application status
            $table->enum('status', ['pending', 'shortlisted', 'interviewed', 'hired', 'rejected'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('responded_at')->nullable();

            // Metadata
            $table->boolean('is_premium')->default(false); // If they paid for premium application
            $table->decimal('application_fee', 8, 2)->nullable(); // Fee paid for application

            $table->timestamps();

            $table->unique(['job_post_id', 'user_id']); // Prevent duplicate applications
            $table->index(['job_post_id', 'status']);
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_applications');
    }
};
