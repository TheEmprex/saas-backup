<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class() extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE job_posts MODIFY COLUMN contract_type ENUM('full_time', 'part_time', 'contract') DEFAULT 'part_time'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE job_posts MODIFY COLUMN contract_type ENUM('full_time', 'part_time', 'project_based') DEFAULT 'part_time'");
    }
};
