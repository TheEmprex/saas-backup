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
            $table->decimal('total_earnings', 10, 2)->default(0)->after('email_verified_at');
            $table->decimal('monthly_earnings', 10, 2)->default(0)->after('total_earnings');
            $table->integer('profile_views')->default(0)->after('monthly_earnings');
            $table->timestamp('last_active_at')->nullable()->after('profile_views');
            $table->json('dashboard_preferences')->nullable()->after('last_active_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'total_earnings',
                'monthly_earnings',
                'profile_views',
                'last_active_at',
                'dashboard_preferences'
            ]);
        });
    }
};
