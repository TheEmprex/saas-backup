<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::table('job_posts', function (Blueprint $table): void {
            $table->text('benefits')->nullable();
            $table->integer('expected_hours_per_week')->nullable();
            $table->integer('duration_months')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('job_posts', function (Blueprint $table): void {
            $table->dropColumn(['benefits', 'expected_hours_per_week', 'duration_months']);
        });
    }
};
