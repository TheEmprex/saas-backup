<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('user_profiles', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_type_id')->constrained()->onDelete('cascade');

            // KYC Information
            $table->boolean('kyc_verified')->default(false);
            $table->string('kyc_document_type')->nullable(); // passport, national_id
            $table->string('kyc_document_number')->nullable();
            $table->string('kyc_document_path')->nullable();
            $table->timestamp('kyc_verified_at')->nullable();

            // Chatter-specific fields
            $table->integer('typing_speed_wpm')->nullable();
            $table->integer('english_proficiency_score')->nullable();
            $table->json('experience_agencies')->nullable(); // Array of past agencies
            $table->json('traffic_sources')->nullable(); // DA, Reddit, Twitter, etc.
            $table->string('availability_timezone')->nullable();
            $table->json('availability_hours')->nullable(); // Working hours

            // Agency-specific fields
            $table->string('company_name')->nullable();
            $table->text('company_description')->nullable();
            $table->string('stripe_account_id')->nullable();
            $table->string('paxum_account_id')->nullable();
            $table->json('results_screenshots')->nullable(); // Array of uploaded screenshots
            $table->json('team_members')->nullable(); // For chatting agencies

            // Common fields
            $table->decimal('average_rating', 3, 2)->default(0.00);
            $table->integer('total_ratings')->default(0);
            $table->integer('jobs_completed')->default(0);
            $table->text('bio')->nullable();
            $table->json('portfolio_links')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index(['user_type_id', 'is_active']);
            $table->index(['kyc_verified', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};
