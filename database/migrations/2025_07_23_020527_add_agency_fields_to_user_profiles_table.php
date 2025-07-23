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
            // Check if columns don't already exist before adding them
            if (!Schema::hasColumn('user_profiles', 'monthly_revenue')) {
                $table->enum('monthly_revenue', ['0-5k', '5-10k', '10-25k', '25-50k', '50-100k', '100-250k', '250k-1m', '1m+'])->nullable();
            }
            
            if (!Schema::hasColumn('user_profiles', 'traffic_types')) {
                $table->json('traffic_types')->nullable(); // Store as JSON array
            }
            
            if (!Schema::hasColumn('user_profiles', 'timezone')) {
                $table->string('timezone', 50)->nullable();
            }
            
            if (!Schema::hasColumn('user_profiles', 'availability_hours')) {
                $table->json('availability_hours')->nullable(); // Store as JSON with days and hours
            }
            
            if (!Schema::hasColumn('user_profiles', 'shift_requirements')) {
                $table->json('shift_requirements')->nullable(); // For agencies posting jobs
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'monthly_revenue',
                'traffic_types',
                'timezone',
                'availability_hours',
                'shift_requirements'
            ]);
        });
    }
};
