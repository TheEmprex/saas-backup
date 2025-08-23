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
        Schema::table('job_posts', function (Blueprint $table) {
            // Add TBD (To be decided) flags for each rate type
            $table->boolean('hourly_rate_tbd')->default(false)->after('hourly_rate');
            $table->boolean('fixed_rate_tbd')->default(false)->after('fixed_rate');
            $table->boolean('commission_rate_tbd')->default(false)->after('commission_percentage');
            
            // Add period for fixed rate (weekly, monthly, total)
            $table->enum('fixed_rate_period', ['total', 'weekly', 'monthly'])->default('total')->after('fixed_rate_tbd');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_posts', function (Blueprint $table) {
            $table->dropColumn(['hourly_rate_tbd', 'fixed_rate_tbd', 'commission_rate_tbd', 'fixed_rate_period']);
        });
    }
};
