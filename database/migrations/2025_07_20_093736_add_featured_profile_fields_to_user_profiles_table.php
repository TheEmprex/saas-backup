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
            $table->boolean('is_featured')->default(false);
            $table->timestamp('featured_until')->nullable();
            $table->decimal('featured_payment_amount', 8, 2)->nullable();
            $table->string('featured_payment_id')->nullable();
            $table->timestamp('featured_paid_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'is_featured',
                'featured_until',
                'featured_payment_amount',
                'featured_payment_id',
                'featured_paid_at'
            ]);
        });
    }
};
