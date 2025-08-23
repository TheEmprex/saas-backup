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
            // Only add columns if they don't already exist
            if (!Schema::hasColumn('job_posts', 'required_timezone')) {
                $table->string('required_timezone', 50)->nullable()->after('benefits');
            }
            if (!Schema::hasColumn('job_posts', 'shift_start_time')) {
                $table->time('shift_start_time')->nullable()->after('required_timezone');
            }
            if (!Schema::hasColumn('job_posts', 'shift_end_time')) {
                $table->time('shift_end_time')->nullable()->after('shift_start_time');
            }
            if (!Schema::hasColumn('job_posts', 'required_days')) {
                $table->json('required_days')->nullable()->after('shift_end_time');
            }
            if (!Schema::hasColumn('job_posts', 'timezone_flexible')) {
                $table->boolean('timezone_flexible')->default(false)->after('required_days');
            }
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
