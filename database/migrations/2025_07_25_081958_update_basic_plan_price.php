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
        Schema::table('subscription_plans', function (Blueprint $table) {
            DB::table('subscription_plans')
                ->where('name', 'Basic')
                ->update(['price' => 59.00]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscription_plans', function (Blueprint $table) {
            DB::table('subscription_plans')
                ->where('name', 'Basic')
                ->update(['price' => 0.00]); // Reverse to original price if needed
        });
    }
};
