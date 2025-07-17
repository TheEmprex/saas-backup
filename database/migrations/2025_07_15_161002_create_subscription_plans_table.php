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
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description');
            $table->decimal('price', 8, 2);
            $table->integer('job_posts_per_week');
            $table->integer('chat_applications_per_day');
            $table->boolean('unlimited_chats')->default(false);
            $table->boolean('advanced_filters')->default(false);
            $table->boolean('analytics_dashboard')->default(false);
            $table->boolean('priority_listings')->default(false);
            $table->boolean('featured_status')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
};
