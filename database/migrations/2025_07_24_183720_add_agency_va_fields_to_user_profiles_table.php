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
            // Update monthly_revenue enum to match the new ranges
            if (Schema::hasColumn('user_profiles', 'monthly_revenue')) {
                $table->dropColumn('monthly_revenue');
            }
            $table->enum('monthly_revenue', ['0-5k', '5-10k', '10-25k', '25-50k', '50-100k', '100-250k', '250k-1m', '1m+'])->nullable();
            
            // Add average LTV field for chatting agencies
            if (!Schema::hasColumn('user_profiles', 'average_ltv')) {
                $table->decimal('average_ltv', 10, 2)->nullable()->comment('Average LTV per traffic for chatting agencies');
            }
            
            // Add work hours availability for VAs/chatters (stored as JSON with timezone info)
            if (!Schema::hasColumn('user_profiles', 'work_hours')) {
                $table->json('work_hours')->nullable()->comment('Available work hours with timezone info for VAs/chatters');
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
                'average_ltv',
                'work_hours'
            ]);
        });
    }
};
