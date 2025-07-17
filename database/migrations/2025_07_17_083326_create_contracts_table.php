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
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('contractor_id')->constrained('users')->onDelete('cascade');
            $table->string('contract_type'); // hourly, fixed, commission
            $table->decimal('rate', 10, 2)->nullable(); // hourly rate or fixed amount
            $table->decimal('commission_percentage', 5, 2)->nullable(); // for commission contracts
            $table->text('description');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->enum('status', ['active', 'completed', 'cancelled', 'suspended'])->default('active');
            $table->decimal('total_earned', 10, 2)->default(0);
            $table->integer('hours_worked')->default(0);
            $table->json('earnings_log')->nullable(); // store earning entries
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamps();
            
            $table->index(['employer_id', 'contractor_id']);
            $table->index(['status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
