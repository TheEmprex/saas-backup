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
        Schema::table('contracts', function (Blueprint $table) {
            // Add approval system fields
            $table->enum('approval_status', ['pending', 'accepted', 'rejected'])->default('pending')->after('status');
            $table->timestamp('approved_at')->nullable()->after('approval_status');
            $table->timestamp('rejected_at')->nullable()->after('approved_at');
            $table->text('rejection_reason')->nullable()->after('rejected_at');
            
            // Update existing status enum to include 'draft' for pre-approval contracts
            $table->enum('status', ['draft', 'active', 'completed', 'cancelled', 'suspended'])->default('draft')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropColumn(['approval_status', 'approved_at', 'rejected_at', 'rejection_reason']);
            $table->enum('status', ['active', 'completed', 'cancelled', 'suspended'])->default('active')->change();
        });
    }
};
