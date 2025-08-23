<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\UserType;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create Virtual Assistant user type
        UserType::firstOrCreate(
            ['name' => 'virtual-assistant'],
            [
                'display_name' => 'Virtual Assistant',
                'description' => 'Virtual assistants who can work without completing tests',
                'can_hire_agency_va' => false,
                'can_hire_freelance_va' => false,
                'can_work_as_agency_va' => true,
                'can_work_as_freelance_va' => true,
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        UserType::where('name', 'virtual-assistant')->delete();
    }
};
