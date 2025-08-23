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
            if (!Schema::hasColumn('conversations', 'last_message_at')) {
                $table->timestamp('last_message_at')->nullable()->after('last_message_id');
            }
            if (!Schema::hasColumn('conversations', 'title')) {
                $table->string('title')->nullable()->after('last_message_at');
            }
            if (!Schema::hasColumn('conversations', 'conversation_type')) {
                $table->enum('conversation_type', ['direct', 'group'])->default('direct')->after('title');
            }
            if (!Schema::hasColumn('conversations', 'is_archived')) {
                $table->boolean('is_archived')->default(false)->after('conversation_type');
            }
            if (!Schema::hasColumn('conversations', 'metadata')) {
                $table->json('metadata')->nullable()->after('is_archived');
            }
            if (!Schema::hasColumn('conversations', 'deleted_at')) {
                $table->softDeletes()->after('updated_at');
            }

            // Add indexes for performance
            $table->index(['user1_id', 'user2_id']);
            $table->index(['last_message_at']);
            $table->index(['conversation_type']);
            $table->index(['is_archived']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            $table->dropColumn([
                'last_message_at',
                'title',
                'conversation_type',
                'is_archived',
                'metadata',
                'deleted_at'
            ]);
            
            $table->dropIndex(['user1_id', 'user2_id']);
            $table->dropIndex(['last_message_at']);
            $table->dropIndex(['conversation_type']);
            $table->dropIndex(['is_archived']);
        });
    }
};
