<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('work_shifts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('employment_contract_id')->constrained()->onDelete('cascade');
            $table->foreignId('chatter_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('agency_id')->constrained('users')->onDelete('cascade');

            // Shift details
            $table->timestamp('shift_start');
            $table->timestamp('shift_end')->nullable();
            $table->integer('total_minutes')->nullable(); // Calculated duration
            $table->decimal('hourly_rate', 8, 2); // Rate for this shift
            $table->decimal('total_earnings', 8, 2)->nullable(); // Calculated earnings

            // Shift status
            $table->enum('status', ['scheduled', 'in_progress', 'completed', 'cancelled', 'no_show'])->default('scheduled');

            // Performance notes
            $table->text('shift_notes')->nullable(); // Notes from chatter
            $table->text('agency_notes')->nullable(); // Notes from agency
            $table->json('performance_metrics')->nullable(); // JSON data for performance tracking

            // Review status
            $table->boolean('reviewed_by_agency')->default(false);
            $table->timestamp('reviewed_at')->nullable();

            $table->timestamps();

            $table->index(['employment_contract_id', 'status']);
            $table->index(['chatter_id', 'shift_start']);
            $table->index(['agency_id', 'shift_start']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_shifts');
    }
};
