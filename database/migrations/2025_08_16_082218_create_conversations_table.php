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
        Schema::table('conversations', function (Blueprint $table) {
            //$table->string('type')->default('private'); // private, group, agency_group
            //$table->string('title')->nullable(); // For group conversations
            //$table->text('description')->nullable();
            //$table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            //$table->boolean('is_archived')->default(false);
            //$table->timestamp('last_activity_at')->nullable();
            //$table->json('metadata')->nullable(); // For additional data

            // Indexes for performance
            //$table->index(['created_by']);
            //table->index(['last_activity_at']);
            //$table->index(['is_archived']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conversations', static function(Blueprint $table) {
            $table->dropColumn([
                'type',
                'title',
                'description',
                'created_by',
                'is_archived',
                'last_activity_at',
                'metadata',
            ]);

            $table->dropIndex(['created_by']);
            $table->dropIndex(['last_activity_at']);
            $table->dropIndex(['is_archived']);
        });
    }
};
