<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Users table optimizations
        Schema::table('users', function (Blueprint $table) {
            // Index for authentication lookups
            if (!$this->indexExists('users', 'users_email_verified_at_index')) {
                $table->index(['email', 'email_verified_at'], 'users_email_verified_at_index');
            }
            
            // Index for active users
            if (!$this->indexExists('users', 'users_active_status_index')) {
                $table->index(['is_banned', 'email_verified_at'], 'users_active_status_index');
            }
            
            // Index for user type filtering
            if (!$this->indexExists('users', 'users_type_status_index')) {
                $table->index(['user_type_id', 'is_banned'], 'users_type_status_index');
            }
            
            // Index for last seen queries
            if (!$this->indexExists('users', 'users_last_seen_index')) {
                $table->index('last_seen_at');
            }
        });

        // Job posts table optimizations
        if (Schema::hasTable('job_posts')) {
            Schema::table('job_posts', function (Blueprint $table) {
                // Compound index for active job searches
                if (!$this->indexExists('job_posts', 'job_posts_active_search_index')) {
                    $table->index(['status', 'expires_at', 'created_at'], 'job_posts_active_search_index');
                }
                
                // Index for featured jobs
                if (!$this->indexExists('job_posts', 'job_posts_featured_index')) {
                    $table->index(['is_featured', 'status'], 'job_posts_featured_index');
                }
                
                // Index for user's jobs
                if (!$this->indexExists('job_posts', 'job_posts_user_status_index')) {
                    $table->index(['user_id', 'status'], 'job_posts_user_status_index');
                }
            });
        }

        // Add more optimizations for other tables safely
        $this->addSafeIndexes();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Users table
        Schema::table('users', function (Blueprint $table) {
            $this->dropIndexIfExists($table, 'users_email_verified_at_index');
            $this->dropIndexIfExists($table, 'users_active_status_index');
            $this->dropIndexIfExists($table, 'users_type_status_index');
            $this->dropIndexIfExists($table, 'users_last_seen_index');
        });

        // Job posts table
        if (Schema::hasTable('job_posts')) {
            Schema::table('job_posts', function (Blueprint $table) {
                $this->dropIndexIfExists($table, 'job_posts_active_search_index');
                $this->dropIndexIfExists($table, 'job_posts_featured_index');
                $this->dropIndexIfExists($table, 'job_posts_user_status_index');
            });
        }
    }

    /**
     * Add additional indexes safely
     */
    private function addSafeIndexes(): void
    {
        // Conversations table optimizations
        if (Schema::hasTable('conversations')) {
            Schema::table('conversations', function (Blueprint $table) {
                if (!$this->indexExists('conversations', 'conversations_user1_updated_index')) {
                    $table->index(['user1_id', 'updated_at'], 'conversations_user1_updated_index');
                }
                
                if (!$this->indexExists('conversations', 'conversations_user2_updated_index')) {
                    $table->index(['user2_id', 'updated_at'], 'conversations_user2_updated_index');
                }
            });
        }

        // Messages table optimizations
        if (Schema::hasTable('messages')) {
            Schema::table('messages', function (Blueprint $table) {
                if (!$this->indexExists('messages', 'messages_conversation_created_index')) {
                    $table->index(['conversation_id', 'created_at'], 'messages_conversation_created_index');
                }
                
                if (!$this->indexExists('messages', 'messages_sender_created_index')) {
                    $table->index(['sender_id', 'created_at'], 'messages_sender_created_index');
                }
            });
        }

        // Sessions table optimizations
        if (Schema::hasTable('sessions')) {
            Schema::table('sessions', function (Blueprint $table) {
                if (!$this->indexExists('sessions', 'sessions_last_activity_index')) {
                    $table->index('last_activity');
                }
                
                if (!$this->indexExists('sessions', 'sessions_user_id_index')) {
                    $table->index('user_id');
                }
            });
        }

        // Job applications table optimizations (if exists)
        if (Schema::hasTable('job_applications')) {
            Schema::table('job_applications', function (Blueprint $table) {
                if (!$this->indexExists('job_applications', 'job_applications_job_status_index')) {
                    $table->index(['job_post_id', 'status'], 'job_applications_job_status_index');
                }
                
                if (!$this->indexExists('job_applications', 'job_applications_user_status_index')) {
                    $table->index(['user_id', 'status'], 'job_applications_user_status_index');
                }
            });
        }
    }

    /**
     * Check if an index exists
     */
    private function indexExists(string $table, string $indexName): bool
    {
        try {
            $indexes = collect(DB::select("SHOW INDEX FROM {$table}"))
                ->pluck('Key_name')
                ->toArray();

            return in_array($indexName, $indexes);
        } catch (\Exception $e) {
            // Table might not exist or other issue
            return false;
        }
    }

    /**
     * Drop an index if it exists
     */
    private function dropIndexIfExists(Blueprint $table, string $indexName): void
    {
        try {
            $table->dropIndex($indexName);
        } catch (\Exception $e) {
            // Index doesn't exist, ignore
        }
    }
};
