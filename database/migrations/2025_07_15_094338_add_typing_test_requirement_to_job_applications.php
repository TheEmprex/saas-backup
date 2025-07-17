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
        Schema::table('job_applications', function (Blueprint $table) {
            $table->integer('typing_test_wpm')->nullable();
            $table->integer('typing_test_accuracy')->nullable();
            $table->timestamp('typing_test_taken_at')->nullable();
            $table->json('typing_test_results')->nullable(); // Store full test results
            $table->boolean('typing_test_passed')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_applications', function (Blueprint $table) {
            $table->dropColumn([
                'typing_test_wpm',
                'typing_test_accuracy', 
                'typing_test_taken_at',
                'typing_test_results',
                'typing_test_passed'
            ]);
        });
    }
};
