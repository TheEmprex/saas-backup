<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::table('subscription_plans', function (Blueprint $table): void {
            // Rename columns to match the model
            $table->renameColumn('job_posts_per_week', 'job_post_limit');
            $table->renameColumn('chat_applications_per_day', 'chat_application_limit');
            $table->renameColumn('analytics_dashboard', 'analytics');

            // Allow null for unlimited limits
            $table->integer('job_post_limit')->nullable()->change();
            $table->integer('chat_application_limit')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('subscription_plans', function (Blueprint $table): void {
            // Reverse the changes
            $table->renameColumn('job_post_limit', 'job_posts_per_week');
            $table->renameColumn('chat_application_limit', 'chat_applications_per_day');
            $table->renameColumn('analytics', 'analytics_dashboard');

            // Make columns not nullable again
            $table->integer('job_posts_per_week')->nullable(false)->change();
            $table->integer('chat_applications_per_day')->nullable(false)->change();
        });
    }
};
