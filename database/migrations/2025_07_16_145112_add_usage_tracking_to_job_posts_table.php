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
            $table->decimal('featured_cost', 8, 2)->nullable()->after('is_featured');
            $table->decimal('urgent_cost', 8, 2)->nullable()->after('is_urgent');
            $table->boolean('feature_payment_required')->default(false)->after('urgent_cost');
            $table->enum('payment_status', ['pending', 'completed', 'failed'])->default('completed')->after('feature_payment_required');
            $table->string('payment_intent_id')->nullable()->after('payment_status');
            $table->timestamp('payment_completed_at')->nullable()->after('payment_intent_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_posts', function (Blueprint $table) {
            $table->dropColumn([
                'featured_cost',
                'urgent_cost', 
                'feature_payment_required',
                'payment_status',
                'payment_intent_id',
                'payment_completed_at'
            ]);
        });
    }
};
