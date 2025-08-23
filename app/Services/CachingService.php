<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CachingService
{
    private const DEFAULT_TTL = 3600; // 1 hour
    private const SHORT_TTL = 300; // 5 minutes
    private const LONG_TTL = 86400; // 24 hours

    /**
     * Cache user-specific data
     */
    public function cacheUserData(int $userId, string $key, $data, int $ttl = self::DEFAULT_TTL): bool
    {
        $cacheKey = $this->getUserCacheKey($userId, $key);
        return Cache::put($cacheKey, $data, $ttl);
    }

    /**
     * Get cached user data
     */
    public function getCachedUserData(int $userId, string $key, $default = null)
    {
        $cacheKey = $this->getUserCacheKey($userId, $key);
        return Cache::get($cacheKey, $default);
    }

    /**
     * Cache conversation data
     */
    public function cacheConversations(int $userId, Collection $conversations): bool
    {
        return $this->cacheUserData($userId, 'conversations', $conversations, self::SHORT_TTL);
    }

    /**
     * Get cached conversations
     */
    public function getCachedConversations(int $userId): ?Collection
    {
        return $this->getCachedUserData($userId, 'conversations');
    }

    /**
     * Cache job listings with filters
     */
    public function cacheJobListings(array $filters, LengthAwarePaginator $jobs): bool
    {
        $cacheKey = $this->getJobListingsCacheKey($filters);
        return Cache::put($cacheKey, $jobs, self::SHORT_TTL);
    }

    /**
     * Get cached job listings
     */
    public function getCachedJobListings(array $filters): ?LengthAwarePaginator
    {
        $cacheKey = $this->getJobListingsCacheKey($filters);
        return Cache::get($cacheKey);
    }

    /**
     * Cache user profile data
     */
    public function cacheUserProfile(int $userId, array $profile): bool
    {
        return $this->cacheUserData($userId, 'profile', $profile, self::LONG_TTL);
    }

    /**
     * Get cached user profile
     */
    public function getCachedUserProfile(int $userId): ?array
    {
        return $this->getCachedUserData($userId, 'profile');
    }

    /**
     * Cache dashboard stats
     */
    public function cacheDashboardStats(int $userId, array $stats): bool
    {
        return $this->cacheUserData($userId, 'dashboard_stats', $stats, self::SHORT_TTL);
    }

    /**
     * Get cached dashboard stats
     */
    public function getCachedDashboardStats(int $userId): ?array
    {
        return $this->getCachedUserData($userId, 'dashboard_stats');
    }

    /**
     * Cache online users
     */
    public function cacheOnlineUsers(Collection $users): bool
    {
        return Cache::put('online_users', $users, self::SHORT_TTL);
    }

    /**
     * Get cached online users
     */
    public function getCachedOnlineUsers(): ?Collection
    {
        return Cache::get('online_users');
    }

    /**
     * Cache application settings
     */
    public function cacheAppSettings(array $settings): bool
    {
        return Cache::put('app_settings', $settings, self::LONG_TTL);
    }

    /**
     * Get cached application settings
     */
    public function getCachedAppSettings(): ?array
    {
        return Cache::get('app_settings');
    }

    /**
     * Cache frequently accessed model data
     */
    public function cacheModel(Model $model, int $ttl = self::DEFAULT_TTL): bool
    {
        $cacheKey = $this->getModelCacheKey($model);
        return Cache::put($cacheKey, $model->toArray(), $ttl);
    }

    /**
     * Get cached model data
     */
    public function getCachedModel(string $modelClass, int $id): ?array
    {
        $cacheKey = $this->getModelCacheKey($modelClass, $id);
        return Cache::get($cacheKey);
    }

    /**
     * Invalidate user cache
     */
    public function invalidateUserCache(int $userId): bool
    {
        $pattern = "user:{$userId}:*";
        return $this->invalidateCachePattern($pattern);
    }

    /**
     * Invalidate conversation cache for users
     */
    public function invalidateConversationCache(array $userIds): bool
    {
        $success = true;
        foreach ($userIds as $userId) {
            $cacheKey = $this->getUserCacheKey($userId, 'conversations');
            $success = $success && Cache::forget($cacheKey);
        }
        return $success;
    }

    /**
     * Invalidate job listings cache
     */
    public function invalidateJobListingsCache(): bool
    {
        return $this->invalidateCachePattern('job_listings:*');
    }

    /**
     * Invalidate model cache
     */
    public function invalidateModelCache(Model $model): bool
    {
        $cacheKey = $this->getModelCacheKey($model);
        return Cache::forget($cacheKey);
    }

    /**
     * Warm up cache for frequently accessed data
     */
    public function warmUpCache(): void
    {
        // Cache frequently accessed settings
        $settings = config('app');
        $this->cacheAppSettings($settings);

        // Cache user types
        $userTypes = \App\Models\UserType::all();
        Cache::put('user_types', $userTypes, self::LONG_TTL);

        // Cache popular jobs
        $popularJobs = \App\Models\JobPost::activeAndNotExpired()
            ->withOptimizedRelations()
            ->popular()
            ->limit(20)
            ->get();
        Cache::put('popular_jobs', $popularJobs, self::SHORT_TTL);
    }

    /**
     * Get cache statistics
     */
    public function getCacheStats(): array
    {
        if (config('cache.default') === 'redis') {
            $redis = Redis::connection();
            $info = $redis->info();
            
            return [
                'driver' => 'redis',
                'memory_usage' => $info['used_memory_human'] ?? 'N/A',
                'hits' => $info['keyspace_hits'] ?? 0,
                'misses' => $info['keyspace_misses'] ?? 0,
                'keys' => $redis->dbSize(),
                'connected_clients' => $info['connected_clients'] ?? 0,
            ];
        }

        return [
            'driver' => config('cache.default'),
            'message' => 'Cache statistics not available for this driver'
        ];
    }

    /**
     * Clear specific cache tags
     */
    public function clearTaggedCache(array $tags): bool
    {
        try {
            Cache::tags($tags)->flush();
            return true;
        } catch (\Exception $e) {
            logger()->error('Failed to clear tagged cache', [
                'tags' => $tags,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Cache with tags for better organization
     */
    public function cacheWithTags(array $tags, string $key, $data, int $ttl = self::DEFAULT_TTL): bool
    {
        try {
            Cache::tags($tags)->put($key, $data, $ttl);
            return true;
        } catch (\Exception $e) {
            logger()->error('Failed to cache with tags', [
                'tags' => $tags,
                'key' => $key,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Get data from tagged cache
     */
    public function getFromTaggedCache(array $tags, string $key, $default = null)
    {
        try {
            return Cache::tags($tags)->get($key, $default);
        } catch (\Exception $e) {
            logger()->error('Failed to get from tagged cache', [
                'tags' => $tags,
                'key' => $key,
                'error' => $e->getMessage()
            ]);
            return $default;
        }
    }

    /**
     * Generate cache key for user-specific data
     */
    private function getUserCacheKey(int $userId, string $key): string
    {
        return "user:{$userId}:{$key}";
    }

    /**
     * Generate cache key for job listings
     */
    private function getJobListingsCacheKey(array $filters): string
    {
        ksort($filters);
        $filterHash = md5(serialize($filters));
        return "job_listings:{$filterHash}";
    }

    /**
     * Generate cache key for model
     */
    private function getModelCacheKey($model, int $id = null): string
    {
        if ($model instanceof Model) {
            $class = get_class($model);
            $id = $model->getKey();
        } else {
            $class = $model;
        }
        
        $className = class_basename($class);
        return "model:{$className}:{$id}";
    }

    /**
     * Invalidate cache by pattern (Redis only)
     */
    private function invalidateCachePattern(string $pattern): bool
    {
        if (config('cache.default') !== 'redis') {
            return false;
        }

        try {
            $redis = Redis::connection();
            $keys = $redis->keys($pattern);
            
            if (!empty($keys)) {
                $redis->del($keys);
            }
            
            return true;
        } catch (\Exception $e) {
            logger()->error('Failed to invalidate cache pattern', [
                'pattern' => $pattern,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}
