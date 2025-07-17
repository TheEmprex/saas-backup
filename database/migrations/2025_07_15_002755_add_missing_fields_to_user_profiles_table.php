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
        Schema::table('user_profiles', function (Blueprint $table) {
            // Basic profile fields
            $table->string('location')->nullable();
            $table->string('website')->nullable();
            $table->string('phone')->nullable();
            $table->integer('experience_years')->nullable();
            $table->json('languages')->nullable();
            $table->json('skills')->nullable();
            $table->text('availability')->nullable();
            $table->decimal('hourly_rate', 8, 2)->nullable();
            $table->enum('preferred_rate_type', ['hourly', 'fixed', 'commission'])->nullable();
            $table->string('portfolio_url')->nullable();
            $table->string('linkedin_url')->nullable();
            $table->integer('views')->default(0);
            
            // Typing test fields
            $table->integer('typing_accuracy')->nullable();
            $table->timestamp('typing_test_taken_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'location',
                'website',
                'phone',
                'experience_years',
                'languages',
                'skills',
                'availability',
                'hourly_rate',
                'preferred_rate_type',
                'portfolio_url',
                'linkedin_url',
                'views',
                'typing_accuracy',
                'typing_test_taken_at',
            ]);
        });
    }
};
