<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add indexes for conversations table
        Schema::table('conversations', function (Blueprint $table) {
            // Composite index for efficient user conversation queries
            if (!$this->indexExists('conversations', 'conversations_user1_activity_index')) {
                $table->index(['user1_id', 'last_message_at', 'updated_at'], 'conversations_user1_activity_index');
            }
            if (!$this->indexExists('conversations', 'conversations_user2_activity_index')) {
                $table->index(['user2_id', 'last_message_at', 'updated_at'], 'conversations_user2_activity_index');
            }
            
            // Index for conversation type filtering
            if (!$this->indexExists('conversations', 'conversations_type_archived_index')) {
                $table->index(['conversation_type', 'is_archived'], 'conversations_type_archived_index');
            }
        });

        // Add indexes for messages table
        Schema::table('messages', function (Blueprint $table) {
            // Composite index for conversation messages with pagination
            if (!$this->indexExists('messages', 'messages_conversation_created_index')) {
                $table->index(['conversation_id', 'created_at'], 'messages_conversation_created_index');
            }
            
            // Index for unread messages queries
            if (!$this->indexExists('messages', 'messages_sender_read_index')) {
                $table->index(['sender_id', 'is_read'], 'messages_sender_read_index');
            }
            
            // Index for message type filtering
            if (!$this->indexExists('messages', 'messages_type_created_index')) {
                $table->index(['message_type', 'created_at'], 'messages_type_created_index');
            }
            
            // Index for reply queries
            if (!$this->indexExists('messages', 'messages_reply_to_index')) {
                $table->index('reply_to_id', 'messages_reply_to_index');
            }
        });

        // Add indexes for job_posts table
        Schema::table('job_posts', function (Blueprint $table) {
            // Composite index for active job queries with ordering
            if (!$this->indexExists('job_posts', 'job_posts_active_priority_index')) {
                $table->index(['status', 'expires_at', 'is_featured', 'is_urgent', 'created_at'], 'job_posts_active_priority_index');
            }
            
            // Index for user job queries  
            if (!$this->indexExists('job_posts', 'job_posts_user_status_index')) {
                $table->index(['user_id', 'status', 'created_at'], 'job_posts_user_status_index');
            }
            
            // Index for filtering by market and experience level
            if (!$this->indexExists('job_posts', 'job_posts_market_exp_status_index')) {
                $table->index(['market', 'experience_level', 'status'], 'job_posts_market_exp_status_index');
            }
            
            // Index for rate filtering
            if (!$this->indexExists('job_posts', 'job_posts_rate_index')) {
                $table->index(['rate_type', 'hourly_rate', 'fixed_rate'], 'job_posts_rate_index');
            }
            
            // Index for timezone filtering
            if (!$this->indexExists('job_posts', 'job_posts_timezone_status_index')) {
                $table->index(['required_timezone', 'status'], 'job_posts_timezone_status_index');
            }
        });

        // Add indexes for job_applications table
        Schema::table('job_applications', function (Blueprint $table) {
            // Composite index for user applications
            if (!$this->indexExists('job_applications', 'job_applications_user_status_index')) {
                $table->index(['user_id', 'status', 'created_at'], 'job_applications_user_status_index');
            }
            
            // Composite index for job applications
            if (!$this->indexExists('job_applications', 'job_applications_job_status_index')) {
                $table->index(['job_post_id', 'status', 'created_at'], 'job_applications_job_status_index');
            }
        });

        // Add indexes for users table
        Schema::table('users', function (Blueprint $table) {
            // Index for user type filtering
            if (!$this->indexExists('users', 'users_type_banned_index')) {
                $table->index(['user_type_id', 'is_banned'], 'users_type_banned_index');
            }
            
            // Index for availability filtering
            if (!$this->indexExists('users', 'users_available_type_index')) {
                $table->index(['available_for_work', 'user_type_id'], 'users_available_type_index');
            }
            
            // Index for last activity
            if (!$this->indexExists('users', 'users_last_seen_index')) {
                $table->index('last_seen_at', 'users_last_seen_index');
            }
            
            // Index for KYC status
            if (!$this->indexExists('users', 'users_kyc_status_index')) {
                $table->index('kyc_status', 'users_kyc_status_index');
            }
        });

        // Add indexes for user_profiles table
        if (Schema::hasTable('user_profiles')) {
            Schema::table('user_profiles', function (Blueprint $table) {
                // Index for hourly rate filtering
                if (!$this->indexExists('user_profiles', 'user_profiles_rate_available_index')) {
                    $table->index(['hourly_rate', 'is_available'], 'user_profiles_rate_available_index');
                }
                
                // Index for rating filtering (using average_rating that exists)
                if (!$this->indexExists('user_profiles', 'user_profiles_rating_reviews_index')) {
                    $table->index(['average_rating', 'total_ratings'], 'user_profiles_rating_reviews_index');
                }
                
                // Index for featured profiles
                if (!$this->indexExists('user_profiles', 'user_profiles_featured_index')) {
                    $table->index(['is_featured', 'featured_until'], 'user_profiles_featured_index');
                }
            });
        }

        // Add indexes for ratings table
        if (Schema::hasTable('ratings')) {
            Schema::table('ratings', function (Blueprint $table) {
                // Composite index for user ratings
                if (!$this->indexExists('ratings', 'ratings_rated_rating_index')) {
                    $table->index(['rated_id', 'overall_rating', 'created_at'], 'ratings_rated_rating_index');
                }
                
                // Index for rater queries
                if (!$this->indexExists('ratings', 'ratings_rater_created_index')) {
                    $table->index(['rater_id', 'created_at'], 'ratings_rater_created_index');
                }
                
                // Index for job-related ratings
                if (!$this->indexExists('ratings', 'ratings_job_rating_index')) {
                    $table->index(['job_post_id', 'overall_rating'], 'ratings_job_rating_index');
                }
            });
        }

        // Add indexes for user_online_statuses table
        if (Schema::hasTable('user_online_statuses')) {
            Schema::table('user_online_statuses', function (Blueprint $table) {
                // Index for online users queries
                if (!$this->indexExists('user_online_statuses', 'online_statuses_online_seen_index')) {
                    $table->index(['is_online', 'last_seen_at'], 'online_statuses_online_seen_index');
                }
            });
        }

        // Add indexes for typing_indicators table
        if (Schema::hasTable('typing_indicators')) {
            Schema::table('typing_indicators', function (Blueprint $table) {
                // Composite index for active typing queries
                if (!$this->indexExists('typing_indicators', 'typing_indicators_conv_started_index')) {
                    $table->index(['conversation_id', 'started_at'], 'typing_indicators_conv_started_index');
                }
            });
        }

        // Add indexes for contracts table if exists
        if (Schema::hasTable('contracts')) {
            Schema::table('contracts', function (Blueprint $table) {
                // Index for employer contracts
                if (!$this->indexExists('contracts', 'contracts_employer_status_index')) {
                    $table->index(['employer_id', 'status', 'created_at'], 'contracts_employer_status_index');
                }
                
                // Index for contractor contracts
                if (!$this->indexExists('contracts', 'contracts_contractor_status_index')) {
                    $table->index(['contractor_id', 'status', 'created_at'], 'contracts_contractor_status_index');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop indexes for conversations table
        Schema::table('conversations', function (Blueprint $table) {
            $table->dropIndex('conversations_user1_activity_index');
            $table->dropIndex('conversations_user2_activity_index');
            $table->dropIndex('conversations_type_archived_index');
        });

        // Drop indexes for messages table
        Schema::table('messages', function (Blueprint $table) {
            $table->dropIndex('messages_conversation_created_index');
            $table->dropIndex('messages_sender_read_index');
            $table->dropIndex('messages_type_created_index');
            $table->dropIndex('messages_reply_to_index');
        });

        // Drop indexes for job_posts table
        Schema::table('job_posts', function (Blueprint $table) {
            $table->dropIndex('job_posts_active_priority_index');
            $table->dropIndex('job_posts_user_status_index');
            $table->dropIndex('job_posts_market_exp_status_index');
            $table->dropIndex('job_posts_rate_index');
            $table->dropIndex('job_posts_timezone_status_index');
        });

        // Drop indexes for job_applications table
        Schema::table('job_applications', function (Blueprint $table) {
            $table->dropIndex('job_applications_user_status_index');
            $table->dropIndex('job_applications_job_status_index');
        });

        // Drop indexes for users table
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_type_banned_index');
            $table->dropIndex('users_available_type_index');
            $table->dropIndex('users_last_seen_index');
            $table->dropIndex('users_kyc_status_index');
        });

        // Drop indexes for user_profiles table
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->dropIndex('user_profiles_rate_available_index');
            $table->dropIndex('user_profiles_rating_reviews_index');
            $table->dropIndex('user_profiles_featured_index');
        });

        // Drop indexes for ratings table
        Schema::table('ratings', function (Blueprint $table) {
            $table->dropIndex('ratings_rated_rating_index');
            $table->dropIndex('ratings_rater_created_index');
            $table->dropIndex('ratings_job_rating_index');
        });

        // Drop indexes for user_online_statuses table
        Schema::table('user_online_statuses', function (Blueprint $table) {
            $table->dropIndex('online_statuses_online_seen_index');
        });

        // Drop indexes for typing_indicators table
        Schema::table('typing_indicators', function (Blueprint $table) {
            $table->dropIndex('typing_indicators_conv_started_index');
        });

        // Drop indexes for contracts table if exists
        if (Schema::hasTable('contracts')) {
            Schema::table('contracts', function (Blueprint $table) {
                $table->dropIndex('contracts_employer_status_index');
                $table->dropIndex('contracts_contractor_status_index');
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
};
