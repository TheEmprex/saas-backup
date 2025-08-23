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
        Schema::table('users', function (Blueprint $table) {
            $table->string('timezone')->default('UTC')->after('email_verified_at');
            $table->json('availability_hours')->nullable()->after('timezone'); // Store weekly availability
            $table->boolean('available_for_work')->default(false)->after('availability_hours');
            $table->decimal('hourly_rate', 8, 2)->nullable()->after('available_for_work');
            $table->string('preferred_currency', 3)->default('USD')->after('hourly_rate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'timezone',
                'availability_hours',
                'available_for_work',
                'hourly_rate',
                'preferred_currency'
            ]);
        });
    }
};
