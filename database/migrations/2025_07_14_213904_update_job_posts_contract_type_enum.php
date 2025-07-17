<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE job_posts MODIFY COLUMN contract_type ENUM('full_time', 'part_time', 'contract') DEFAULT 'part_time'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE job_posts MODIFY COLUMN contract_type ENUM('full_time', 'part_time', 'project_based') DEFAULT 'part_time'");
    }
};
