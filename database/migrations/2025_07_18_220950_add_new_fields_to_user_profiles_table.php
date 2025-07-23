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
            // Add services field (JSON array)
            $table->json('services')->nullable()->after('skills');
            
            // Add availability status (boolean)
            $table->boolean('is_available')->default(true)->after('is_active');
            
            // Add response time field
            $table->string('response_time')->nullable()->after('is_available');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->dropColumn(['services', 'is_available', 'response_time']);
        });
    }
};
