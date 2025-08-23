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
        // Index pour conversations
        Schema::table('conversations', function (Blueprint $table) {
            // Vérifier et créer les index seulement s'ils n'existent pas
            if (!Schema::hasIndex('conversations', 'idx_conversations_user1')) {
                $table->index(['user1_id'], 'idx_conversations_user1');
            }
            if (!Schema::hasIndex('conversations', 'idx_conversations_user2')) {
                $table->index(['user2_id'], 'idx_conversations_user2');
            }
            if (!Schema::hasIndex('conversations', 'idx_conversations_users')) {
                $table->index(['user1_id', 'user2_id'], 'idx_conversations_users');
            }
            if (!Schema::hasIndex('conversations', 'idx_conversations_updated_at')) {
                $table->index(['updated_at'], 'idx_conversations_updated_at');
            }
            if (!Schema::hasIndex('conversations', 'idx_conversations_last_message_at')) {
                $table->index(['last_message_at'], 'idx_conversations_last_message_at');
            }
        });

        // Index pour messages  
        Schema::table('messages', function (Blueprint $table) {
            // Index composite conversation + created_at (très fréquent)
            if (!Schema::hasIndex('messages', 'idx_messages_conversation_created')) {
                $table->index(['conversation_id', 'created_at'], 'idx_messages_conversation_created');
            }
            // Index sur sender pour analytics
            if (!Schema::hasIndex('messages', 'idx_messages_sender_created')) {
                $table->index(['sender_id', 'created_at'], 'idx_messages_sender_created');
            }
            // Index sur read_by pour status - Skip car c'est JSON
            // $table->index(['read_by'], 'idx_messages_read_by'); // JSON column can't be indexed directly
            // Index pour soft deletes si utilisé
            if (Schema::hasColumn('messages', 'deleted_at') && !Schema::hasIndex('messages', 'idx_messages_deleted_at')) {
                $table->index(['deleted_at'], 'idx_messages_deleted_at');
            }
        });

        // Index pour users (online status, etc.)
        Schema::table('users', function (Blueprint $table) {
            // Index sur last_seen_at pour status en ligne
            if (Schema::hasColumn('users', 'last_seen_at') && !Schema::hasIndex('users', 'idx_users_last_seen')) {
                $table->index(['last_seen_at'], 'idx_users_last_seen');
            }
            // Index sur verification status
            if (Schema::hasColumn('users', 'kyc_status') && !Schema::hasIndex('users', 'idx_users_kyc_status')) {
                $table->index(['kyc_status'], 'idx_users_kyc_status');
            }
            // Index pour recherche utilisateurs actifs
            if (!Schema::hasIndex('users', 'idx_users_activity')) {
                $table->index(['created_at', 'updated_at'], 'idx_users_activity');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            $table->dropIndex('idx_conversations_user1');
            $table->dropIndex('idx_conversations_user2');
            $table->dropIndex('idx_conversations_users');
            $table->dropIndex('idx_conversations_updated_at');
            $table->dropIndex('idx_conversations_last_message_at');
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->dropIndex('idx_messages_conversation_created');
            $table->dropIndex('idx_messages_sender_created');
            // $table->dropIndex('idx_messages_read_by'); // Was skipped
            if (Schema::hasIndex('messages', 'idx_messages_deleted_at')) {
                $table->dropIndex('idx_messages_deleted_at');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasIndex('users', 'idx_users_last_seen')) {
                $table->dropIndex('idx_users_last_seen');
            }
            if (Schema::hasIndex('users', 'idx_users_kyc_status')) {
                $table->dropIndex('idx_users_kyc_status');
            }
            $table->dropIndex('idx_users_activity');
        });
    }
};
