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
            $table->string('required_timezone', 50)->nullable(); // Preferred timezone for the job
            $table->time('shift_start_time')->nullable(); // Start time in the required timezone
            $table->time('shift_end_time')->nullable(); // End time in the required timezone
            $table->json('required_days')->nullable(); // Days of week required (e.g., ["monday", "tuesday"])
            $table->boolean('timezone_flexible')->default(false); // Whether timezone is flexible
            
            $table->index(['required_timezone']);
            $table->index(['timezone_flexible']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_posts', function (Blueprint $table) {
            $table->dropColumn([
                'required_timezone',
                'shift_start_time', 
                'shift_end_time',
                'required_days',
                'timezone_flexible'
            ]);
        });
    }
};
