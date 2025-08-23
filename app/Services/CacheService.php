<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class CacheService
{
    /**
     * Cache TTL constants (in seconds)
     */
    const TTL_SHORT = 300;      // 5 minutes
    const TTL_MEDIUM = 1800;    // 30 minutes
    const TTL_LONG = 3600;      // 1 hour
    const TTL_VERY_LONG = 86400; // 24 hours

    /**
     * Cache key prefixes
     */
    const PREFIX_USER = 'user:';
    const PREFIX_JOB = 'job:';
    const PREFIX_CONVERSATION = 'conversation:';
    const PREFIX_MESSAGE = 'message:';
    const PREFIX_SEARCH = 'search:';
    const PREFIX_STATS = 'stats:';

    /**
     * Cache a user profile with related data
     */
    public function cacheUserProfile(int $userId, array $data, int $ttl = self::TTL_MEDIUM): void
    {
        $key = self::PREFIX_USER . "profile:{$userId}";
        
        Cache::put($key, $data, $ttl);
        
        // Also cache individual fields for quick access
        Cache::put($key . ':name', $data['name'] ?? null, $ttl);
        Cache::put($key . ':email', $data['email'] ?? null, $ttl);
        Cache::put($key . ':avatar', $data['avatar'] ?? null, $ttl);
    }

    /**
     * Get cached user profile
     */
    public function getUserProfile(int $userId): ?array
    {
        $key = self::PREFIX_USER . "profile:{$userId}";
        return Cache::get($key);
    }

    /**
     * Cache user online status
     */
    public function cacheUserOnlineStatus(int $userId, bool $isOnline, int $ttl = self::TTL_SHORT): void
    {
        $key = self::PREFIX_USER . "online:{$userId}";
        Cache::put($key, $isOnline, $ttl);
        
        // Add to online users set
        if ($isOnline) {
            $this->addToOnlineUsers($userId);
        } else {
            $this->removeFromOnlineUsers($userId);
        }
    }

    /**
     * Get user online status
     */
    public function getUserOnlineStatus(int $userId): ?bool
    {
        $key = self::PREFIX_USER . "online:{$userId}";
        return Cache::get($key);
    }

    /**
     * Add user to online users set
     */
    private function addToOnlineUsers(int $userId): void
    {
        Redis::sadd('online_users', $userId);
        Redis::expire('online_users', self::TTL_MEDIUM);
    }

    /**
     * Remove user from online users set
     */
    private function removeFromOnlineUsers(int $userId): void
    {
        Redis::srem('online_users', $userId);
    }

    /**
     * Get all online users
     */
    public function getOnlineUsers(): array
    {
        return Redis::smembers('online_users') ?: [];
    }

    /**
     * Cache conversation data
     */
    public function cacheConversation(int $conversationId, array $data, int $ttl = self::TTL_MEDIUM): void
    {
        $key = self::PREFIX_CONVERSATION . $conversationId;
        Cache::put($key, $data, $ttl);
    }

    /**
     * Get cached conversation
     */
    public function getConversation(int $conversationId): ?array
    {
        $key = self::PREFIX_CONVERSATION . $conversationId;
        return Cache::get($key);
    }

    /**
     * Cache conversation list for a user
     */
    public function cacheUserConversations(int $userId, array $conversations, int $ttl = self::TTL_MEDIUM): void
    {
        $key = self::PREFIX_USER . "conversations:{$userId}";
        Cache::put($key, $conversations, $ttl);
    }

    /**
     * Get cached user conversations
     */
    public function getUserConversations(int $userId): ?array
    {
        $key = self::PREFIX_USER . "conversations:{$userId}";
        return Cache::get($key);
    }

    /**
     * Cache messages for a conversation
     */
    public function cacheConversationMessages(int $conversationId, int $page, array $messages, int $ttl = self::TTL_SHORT): void
    {
        $key = self::PREFIX_CONVERSATION . "messages:{$conversationId}:page:{$page}";
        Cache::put($key, $messages, $ttl);
    }

    /**
     * Get cached conversation messages
     */
    public function getConversationMessages(int $conversationId, int $page): ?array
    {
        $key = self::PREFIX_CONVERSATION . "messages:{$conversationId}:page:{$page}";
        return Cache::get($key);
    }

    /**
     * Cache job post data
     */
    public function cacheJobPost(int $jobId, array $data, int $ttl = self::TTL_LONG): void
    {
        $key = self::PREFIX_JOB . $jobId;
        Cache::put($key, $data, $ttl);
    }

    /**
     * Get cached job post
     */
    public function getJobPost(int $jobId): ?array
    {
        $key = self::PREFIX_JOB . $jobId;
        return Cache::get($key);
    }

    /**
     * Cache job search results
     */
    public function cacheJobSearch(string $searchHash, array $results, int $ttl = self::TTL_SHORT): void
    {
        $key = self::PREFIX_SEARCH . "jobs:{$searchHash}";
        Cache::put($key, $results, $ttl);
    }

    /**
     * Get cached job search results
     */
    public function getJobSearch(string $searchHash): ?array
    {
        $key = self::PREFIX_SEARCH . "jobs:{$searchHash}";
        return Cache::get($key);
    }

    /**
     * Generate search hash from parameters
     */
    public function generateSearchHash(array $params): string
    {
        ksort($params); // Sort for consistency
        return md5(serialize($params));
    }

    /**
     * Cache user search results
     */
    public function cacheUserSearch(string $searchHash, array $results, int $ttl = self::TTL_SHORT): void
    {
        $key = self::PREFIX_SEARCH . "users:{$searchHash}";
        Cache::put($key, $results, $ttl);
    }

    /**
     * Get cached user search results
     */
    public function getUserSearch(string $searchHash): ?array
    {
        $key = self::PREFIX_SEARCH . "users:{$searchHash}";
        return Cache::get($key);
    }

    /**
     * Cache system statistics
     */
    public function cacheSystemStats(array $stats, int $ttl = self::TTL_SHORT): void
    {
        $key = self::PREFIX_STATS . 'system';
        Cache::put($key, $stats, $ttl);
    }

    /**
     * Get cached system statistics
     */
    public function getSystemStats(): ?array
    {
        $key = self::PREFIX_STATS . 'system';
        return Cache::get($key);
    }

    /**
     * Cache user statistics
     */
    public function cacheUserStats(int $userId, array $stats, int $ttl = self::TTL_LONG): void
    {
        $key = self::PREFIX_STATS . "user:{$userId}";
        Cache::put($key, $stats, $ttl);
    }

    /**
     * Get cached user statistics
     */
    public function getUserStats(int $userId): ?array
    {
        $key = self::PREFIX_STATS . "user:{$userId}";
        return Cache::get($key);
    }

    /**
     * Invalidate user-related caches
     */
    public function invalidateUserCache(int $userId): void
    {
        $patterns = [
            self::PREFIX_USER . "profile:{$userId}*",
            self::PREFIX_USER . "conversations:{$userId}",
            self::PREFIX_USER . "online:{$userId}",
            self::PREFIX_STATS . "user:{$userId}",
        ];

        foreach ($patterns as $pattern) {
            $this->invalidateByPattern($pattern);
        }

        // Remove from online users
        $this->removeFromOnlineUsers($userId);

        Log::info('User cache invalidated', ['user_id' => $userId]);
    }

    /**
     * Invalidate conversation-related caches
     */
    public function invalidateConversationCache(int $conversationId): void
    {
        $patterns = [
            self::PREFIX_CONVERSATION . $conversationId,
            self::PREFIX_CONVERSATION . "messages:{$conversationId}*",
        ];

        foreach ($patterns as $pattern) {
            $this->invalidateByPattern($pattern);
        }

        Log::info('Conversation cache invalidated', ['conversation_id' => $conversationId]);
    }

    /**
     * Invalidate job-related caches
     */
    public function invalidateJobCache(int $jobId): void
    {
        $patterns = [
            self::PREFIX_JOB . $jobId,
            self::PREFIX_SEARCH . "jobs:*",
        ];

        foreach ($patterns as $pattern) {
            $this->invalidateByPattern($pattern);
        }

        Log::info('Job cache invalidated', ['job_id' => $jobId]);
    }

    /**
     * Invalidate search caches
     */
    public function invalidateSearchCache(string $type = null): void
    {
        $patterns = $type ? 
            [self::PREFIX_SEARCH . "{$type}:*"] : 
            [self::PREFIX_SEARCH . "*"];

        foreach ($patterns as $pattern) {
            $this->invalidateByPattern($pattern);
        }

        Log::info('Search cache invalidated', ['type' => $type]);
    }

    /**
     * Invalidate caches by pattern
     */
    private function invalidateByPattern(string $pattern): void
    {
        try {
            if (Cache::getStore() instanceof \Illuminate\Cache\RedisStore) {
                $keys = Redis::keys($pattern);
                if (!empty($keys)) {
                    Redis::del($keys);
                }
            } else {
                // For non-Redis stores, we'll need to track keys manually
                // This is a simplified approach
                Cache::forget($pattern);
            }
        } catch (\Exception $e) {
            Log::warning('Failed to invalidate cache pattern', [
                'pattern' => $pattern,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Cache database query results
     */
    public function cacheQuery(string $sql, array $bindings, $result, int $ttl = self::TTL_MEDIUM): void
    {
        $key = 'query:' . md5($sql . serialize($bindings));
        Cache::put($key, $result, $ttl);
    }

    /**
     * Get cached query results
     */
    public function getCachedQuery(string $sql, array $bindings)
    {
        $key = 'query:' . md5($sql . serialize($bindings));
        return Cache::get($key);
    }

    /**
     * Cache pagination results
     */
    public function cachePagination(string $key, LengthAwarePaginator $paginator, int $ttl = self::TTL_SHORT): void
    {
        $data = [
            'items' => $paginator->items(),
            'total' => $paginator->total(),
            'per_page' => $paginator->perPage(),
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
        ];
        
        Cache::put($key, $data, $ttl);
    }

    /**
     * Get cached pagination results
     */
    public function getCachedPagination(string $key): ?array
    {
        return Cache::get($key);
    }

    /**
     * Warm up critical caches
     */
    public function warmUpCache(): void
    {
        Log::info('Starting cache warm-up');

        try {
            // Warm up system statistics
            // This would typically fetch and cache frequently accessed data
            
            // Warm up popular job posts
            // This would cache the most viewed/applied jobs
            
            // Warm up online users
            // This would refresh the online users set
            
            Log::info('Cache warm-up completed');
        } catch (\Exception $e) {
            Log::error('Cache warm-up failed', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Get cache statistics
     */
    public function getCacheStats(): array
    {
        try {
            if (Cache::getStore() instanceof \Illuminate\Cache\RedisStore) {
                $info = Redis::info();
                
                return [
                    'driver' => 'redis',
                    'connected_clients' => $info['connected_clients'] ?? 0,
                    'used_memory' => $info['used_memory_human'] ?? '0',
                    'total_commands_processed' => $info['total_commands_processed'] ?? 0,
                    'keyspace_hits' => $info['keyspace_hits'] ?? 0,
                    'keyspace_misses' => $info['keyspace_misses'] ?? 0,
                    'hit_rate' => $this->calculateHitRate($info),
                ];
            }
            
            return [
                'driver' => 'file',
                'status' => 'active'
            ];
        } catch (\Exception $e) {
            return [
                'driver' => 'unknown',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Calculate cache hit rate
     */
    private function calculateHitRate(array $info): float
    {
        $hits = $info['keyspace_hits'] ?? 0;
        $misses = $info['keyspace_misses'] ?? 0;
        $total = $hits + $misses;
        
        return $total > 0 ? round(($hits / $total) * 100, 2) : 0;
    }

    /**
     * Clear all application caches
     */
    public function clearAllCache(): void
    {
        Log::info('Clearing all application caches');
        
        try {
            Cache::flush();
            Log::info('All caches cleared successfully');
        } catch (\Exception $e) {
            Log::error('Failed to clear all caches', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Remember a value in cache with callback
     */
    public function remember(string $key, int $ttl, callable $callback)
    {
        return Cache::remember($key, $ttl, $callback);
    }

    /**
     * Remember a value forever in cache with callback
     */
    public function rememberForever(string $key, callable $callback)
    {
        return Cache::rememberForever($key, $callback);
    }
}
