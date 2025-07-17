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
        Schema::create('employment_contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agency_id')->constrained('users')->onDelete('cascade'); // Agency user
            $table->foreignId('chatter_id')->constrained('users')->onDelete('cascade'); // Chatter user
            $table->foreignId('job_application_id')->constrained()->onDelete('cascade'); // Original application
            $table->foreignId('job_post_id')->constrained()->onDelete('cascade'); // Job this is for
            
            // Contract details
            $table->decimal('agreed_rate', 8, 2); // Hourly rate agreed upon
            $table->integer('expected_hours_per_week')->nullable();
            $table->text('contract_terms')->nullable();
            $table->text('special_instructions')->nullable();
            
            // Contract status
            $table->enum('status', ['active', 'terminated', 'completed', 'suspended'])->default('active');
            $table->timestamp('start_date');
            $table->timestamp('end_date')->nullable();
            $table->timestamp('terminated_at')->nullable();
            $table->text('termination_reason')->nullable();
            $table->foreignId('terminated_by')->nullable()->constrained('users'); // Who terminated
            
            // Performance tracking
            $table->decimal('average_rating', 3, 2)->nullable();
            $table->integer('total_shifts')->default(0);
            $table->integer('total_hours_worked')->default(0);
            $table->decimal('total_earnings', 10, 2)->default(0);
            
            $table->timestamps();
            
            $table->unique(['agency_id', 'chatter_id', 'job_post_id']); // Prevent duplicate contracts
            $table->index(['agency_id', 'status']);
            $table->index(['chatter_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employment_contracts');
    }
};
