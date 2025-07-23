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
        Schema::create('earnings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('contract_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('USD');
            $table->enum('type', ['hourly', 'fixed', 'commission', 'bonus', 'tip']);
            $table->enum('status', ['pending', 'paid', 'cancelled'])->default('pending');
            $table->text('description')->nullable();
            $table->date('earned_date');
            $table->date('paid_date')->nullable();
            $table->json('metadata')->nullable(); // For storing additional data
            $table->timestamps();
            
            $table->index(['user_id', 'earned_date']);
            $table->index(['status', 'earned_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('earnings');
    }
};
