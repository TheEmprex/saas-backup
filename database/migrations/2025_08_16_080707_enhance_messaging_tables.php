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
        // Enhance conversations table
        Schema::table('conversations', function (Blueprint $table) {
            if (!Schema::hasColumn('conversations', 'is_pinned')) {
                $table->boolean('is_pinned')->default(false)->after('is_archived');
            }
            if (!Schema::hasColumn('conversations', 'is_muted')) {
                $table->boolean('is_muted')->default(false)->after('is_pinned');
            }
            if (!Schema::hasColumn('conversations', 'theme')) {
                $table->string('theme')->nullable()->after('is_muted');
            }
            if (!Schema::hasColumn('conversations', 'encryption_key')) {
                $table->string('encryption_key')->nullable()->after('theme');
            }
        });
        
        // Enhance messages table
        Schema::table('messages', function (Blueprint $table) {
            if (!Schema::hasColumn('messages', 'local_id')) {
                $table->string('local_id')->nullable()->after('id');
                $table->index('local_id');
            }
            if (!Schema::hasColumn('messages', 'status')) {
                $table->enum('status', ['sending', 'sent', 'delivered', 'read', 'failed'])
                      ->default('sending')
                      ->after('message_type');
            }
            if (!Schema::hasColumn('messages', 'delivered_at')) {
                $table->timestamp('delivered_at')->nullable()->after('edited_at');
            }
            if (!Schema::hasColumn('messages', 'read_at')) {
                $table->timestamp('read_at')->nullable()->after('delivered_at');
            }
            if (!Schema::hasColumn('messages', 'is_system')) {
                $table->boolean('is_system')->default(false)->after('read_at');
            }
            if (!Schema::hasColumn('messages', 'thread_id')) {
                $table->string('thread_id')->nullable()->after('is_system');
                $table->index('thread_id');
            }
        });
        
        // Create message_reactions table if it doesn't exist
        if (!Schema::hasTable('message_reactions')) {
            Schema::create('message_reactions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('message_id')->constrained()->onDelete('cascade');
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('emoji', 10);
                $table->timestamps();
                
                $table->unique(['message_id', 'user_id', 'emoji']);
                $table->index(['message_id', 'emoji']);
            });
        }
        
        // Create conversation_participants table for group conversations
        if (!Schema::hasTable('conversation_participants')) {
            Schema::create('conversation_participants', function (Blueprint $table) {
                $table->id();
                $table->foreignId('conversation_id')->constrained()->onDelete('cascade');
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->enum('role', ['admin', 'member'])->default('member');
                $table->timestamp('joined_at')->useCurrent();
                $table->timestamp('left_at')->nullable();
                $table->json('settings')->nullable(); // mute, notifications, etc.
                $table->timestamps();
                
                $table->unique(['conversation_id', 'user_id']);
            });
        }
        
        // Create message_mentions table
        if (!Schema::hasTable('message_mentions')) {
            Schema::create('message_mentions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('message_id')->constrained()->onDelete('cascade');
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->boolean('is_read')->default(false);
                $table->timestamps();
                
                $table->unique(['message_id', 'user_id']);
            });
        }
        
        // Add indexes for performance (using try/catch to avoid errors if already exist)
        try {
            Schema::table('messages', function (Blueprint $table) {
                $table->index(['conversation_id', 'created_at'], 'msg_conversation_created_idx');
            });
        } catch (Exception $e) { /* Index already exists */ }
        
        try {
            Schema::table('messages', function (Blueprint $table) {
                $table->index(['sender_id', 'created_at'], 'msg_sender_created_idx');
            });
        } catch (Exception $e) { /* Index already exists */ }
        
        try {
            Schema::table('conversations', function (Blueprint $table) {
                $table->index(['user1_id', 'user2_id'], 'conv_users_idx');
            });
        } catch (Exception $e) { /* Index already exists */ }
        
        try {
            Schema::table('conversations', function (Blueprint $table) {
                $table->index('last_message_at', 'conv_last_msg_at_idx');
            });
        } catch (Exception $e) { /* Index already exists */ }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            $table->dropColumn(['is_pinned', 'is_muted', 'theme', 'encryption_key']);
        });
        
        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn(['local_id', 'status', 'delivered_at', 'read_at', 'is_system', 'thread_id']);
        });
        
        Schema::dropIfExists('message_reactions');
        Schema::dropIfExists('conversation_participants');
        Schema::dropIfExists('message_mentions');
    }
};
