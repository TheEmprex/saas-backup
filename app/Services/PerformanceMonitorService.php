<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class PerformanceMonitorService
{
    private const CACHE_TTL = 300; // 5 minutes
    private const ALERT_THRESHOLD = 0.8; // 80% threshold for alerts
    
    /**
     * Monitor application performance metrics
     */
    public function monitorPerformance(): array
    {
        $metrics = [
            'database' => $this->monitorDatabase(),
            'cache' => $this->monitorCache(),
            'memory' => $this->monitorMemory(),
            'queue' => $this->monitorQueue(),
            'storage' => $this->monitorStorage(),
            'response_time' => $this->monitorResponseTime(),
        ];
        
        // Check for alerts
        $this->checkAlerts($metrics);
        
        // Cache metrics for dashboard
        Cache::put('performance_metrics', $metrics, self::CACHE_TTL);
        
        return $metrics;
    }
    
    /**
     * Monitor database performance
     */
    private function monitorDatabase(): array
    {
        $start = microtime(true);
        
        try {
            // Test database connection
            DB::select('SELECT 1');
            $connectionTime = (microtime(true) - $start) * 1000; // Convert to milliseconds
            
            // Get database statistics
            $stats = DB::select('SHOW STATUS LIKE "Threads_connected"');
            $connections = $stats[0]->Value ?? 0;
            
            // Get slow query count (if available)
            $slowQueries = DB::select('SHOW STATUS LIKE "Slow_queries"');
            $slowQueryCount = $slowQueries[0]->Value ?? 0;
            
            return [
                'status' => 'healthy',
                'connection_time' => round($connectionTime, 2),
                'active_connections' => (int) $connections,
                'slow_queries' => (int) $slowQueryCount,
                'health_score' => $this->calculateHealthScore($connectionTime, 100) // Good if < 100ms
            ];
        } catch (\Exception $e) {
            Log::error('Database monitoring failed', ['error' => $e->getMessage()]);
            
            return [
                'status' => 'unhealthy',
                'error' => $e->getMessage(),
                'health_score' => 0
            ];
        }
    }
    
    /**
     * Monitor cache performance
     */
    private function monitorCache(): array
    {
        $start = microtime(true);
        
        try {
            // Test cache connection
            $testKey = 'performance_test_' . time();
            Cache::put($testKey, 'test_value', 60);
            $value = Cache::get($testKey);
            Cache::forget($testKey);
            
            $responseTime = (microtime(true) - $start) * 1000;
            
            // Get Redis info if using Redis
            $redisInfo = [];
            if (config('cache.default') === 'redis') {
                try {
                    $redis = Redis::connection();
                    $info = $redis->info();
                    $redisInfo = [
                        'memory_usage' => $info['used_memory_human'] ?? 'N/A',
                        'connected_clients' => $info['connected_clients'] ?? 0,
                        'keyspace_hits' => $info['keyspace_hits'] ?? 0,
                        'keyspace_misses' => $info['keyspace_misses'] ?? 0,
                    ];
                } catch (\Exception $e) {
                    Log::warning('Redis info collection failed', ['error' => $e->getMessage()]);
                }
            }
            
            return [
                'status' => $value === 'test_value' ? 'healthy' : 'degraded',
                'response_time' => round($responseTime, 2),
                'driver' => config('cache.default'),
                'redis_info' => $redisInfo,
                'health_score' => $this->calculateHealthScore($responseTime, 50) // Good if < 50ms
            ];
        } catch (\Exception $e) {
            Log::error('Cache monitoring failed', ['error' => $e->getMessage()]);
            
            return [
                'status' => 'unhealthy',
                'error' => $e->getMessage(),
                'health_score' => 0
            ];
        }
    }
    
    /**
     * Monitor memory usage
     */
    private function monitorMemory(): array
    {
        $memoryUsage = memory_get_usage(true);
        $memoryPeak = memory_get_peak_usage(true);
        $memoryLimit = $this->parseMemoryLimit(ini_get('memory_limit'));
        
        $usagePercentage = $memoryLimit > 0 ? ($memoryUsage / $memoryLimit) * 100 : 0;
        
        return [
            'current_usage' => $this->formatBytes($memoryUsage),
            'peak_usage' => $this->formatBytes($memoryPeak),
            'memory_limit' => $this->formatBytes($memoryLimit),
            'usage_percentage' => round($usagePercentage, 2),
            'status' => $usagePercentage > 80 ? 'warning' : 'healthy',
            'health_score' => max(0, 100 - $usagePercentage)
        ];
    }
    
    /**
     * Monitor queue performance
     */
    private function monitorQueue(): array
    {
        try {
            // Get failed jobs count
            $failedJobs = DB::table('failed_jobs')->count();
            
            // Get pending jobs count (if using database queue)
            $pendingJobs = 0;
            if (config('queue.default') === 'database') {
                $pendingJobs = DB::table('jobs')->count();
            }
            
            return [
                'driver' => config('queue.default'),
                'failed_jobs' => $failedJobs,
                'pending_jobs' => $pendingJobs,
                'status' => $failedJobs > 50 ? 'warning' : 'healthy',
                'health_score' => max(0, 100 - ($failedJobs * 2)) // Deduct 2 points per failed job
            ];
        } catch (\Exception $e) {
            Log::error('Queue monitoring failed', ['error' => $e->getMessage()]);
            
            return [
                'status' => 'unhealthy',
                'error' => $e->getMessage(),
                'health_score' => 0
            ];
        }
    }
    
    /**
     * Monitor storage usage
     */
    private function monitorStorage(): array
    {
        $storagePath = storage_path();
        $publicPath = public_path('storage');
        
        $storageSize = $this->getDirectorySize($storagePath);
        $publicSize = is_dir($publicPath) ? $this->getDirectorySize($publicPath) : 0;
        
        // Get disk free space
        $freeBytes = disk_free_space($storagePath);
        $totalBytes = disk_total_space($storagePath);
        $usedPercentage = $totalBytes > 0 ? (($totalBytes - $freeBytes) / $totalBytes) * 100 : 0;
        
        return [
            'storage_size' => $this->formatBytes($storageSize),
            'public_size' => $this->formatBytes($publicSize),
            'disk_free' => $this->formatBytes($freeBytes),
            'disk_total' => $this->formatBytes($totalBytes),
            'disk_usage_percentage' => round($usedPercentage, 2),
            'status' => $usedPercentage > 85 ? 'warning' : 'healthy',
            'health_score' => max(0, 100 - $usedPercentage)
        ];
    }
    
    /**
     * Monitor response time
     */
    private function monitorResponseTime(): array
    {
        // Get average response time from cache (if available)
        $cachedMetrics = Cache::get('response_time_metrics', []);
        
        return [
            'average_response_time' => $cachedMetrics['average'] ?? 0,
            'max_response_time' => $cachedMetrics['max'] ?? 0,
            'min_response_time' => $cachedMetrics['min'] ?? 0,
            'total_requests' => $cachedMetrics['count'] ?? 0,
            'status' => ($cachedMetrics['average'] ?? 0) > 2000 ? 'warning' : 'healthy',
            'health_score' => $this->calculateHealthScore($cachedMetrics['average'] ?? 0, 1000)
        ];
    }
    
    /**
     * Check for performance alerts
     */
    private function checkAlerts(array $metrics): void
    {
        $alerts = [];
        
        foreach ($metrics as $component => $data) {
            if (isset($data['health_score']) && $data['health_score'] < (self::ALERT_THRESHOLD * 100)) {
                $alerts[] = [
                    'component' => $component,
                    'health_score' => $data['health_score'],
                    'status' => $data['status'] ?? 'unknown',
                    'timestamp' => now()
                ];
            }
        }
        
        if (!empty($alerts)) {
            Log::warning('Performance alerts triggered', ['alerts' => $alerts]);
            
            // Store alerts for dashboard
            Cache::put('performance_alerts', $alerts, 3600); // 1 hour
            
            // Send notifications if configured
            $this->sendAlertNotifications($alerts);
        }
    }
    
    /**
     * Send alert notifications
     */
    private function sendAlertNotifications(array $alerts): void
    {
        // Implementation depends on your notification preferences
        // Could send email, Slack, Discord, etc.
        
        foreach ($alerts as $alert) {
            Log::critical("Performance Alert: {$alert['component']} health score dropped to {$alert['health_score']}%");
        }
    }
    
    /**
     * Calculate health score based on response time
     */
    private function calculateHealthScore(float $responseTime, float $goodThreshold): float
    {
        if ($responseTime <= $goodThreshold) {
            return 100;
        }
        
        // Decrease score as response time increases
        $score = 100 - (($responseTime - $goodThreshold) / $goodThreshold * 50);
        return max(0, $score);
    }
    
    /**
     * Parse memory limit string to bytes
     */
    private function parseMemoryLimit(string $limit): int
    {
        if ($limit === '-1') {
            return 0; // Unlimited
        }
        
        $limit = trim($limit);
        $last = strtolower($limit[strlen($limit) - 1]);
        $value = (int) $limit;
        
        switch ($last) {
            case 'g':
                $value *= 1024;
            case 'm':
                $value *= 1024;
            case 'k':
                $value *= 1024;
        }
        
        return $value;
    }
    
    /**
     * Format bytes to human readable format
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes >= 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }
    
    /**
     * Get directory size recursively
     */
    private function getDirectorySize(string $directory): int
    {
        if (!is_dir($directory)) {
            return 0;
        }
        
        $size = 0;
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory, \RecursiveDirectoryIterator::SKIP_DOTS)
        );
        
        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $size += $file->getSize();
            }
        }
        
        return $size;
    }
    
    /**
     * Get overall application health score
     */
    public function getOverallHealthScore(): array
    {
        $metrics = Cache::get('performance_metrics', []);
        
        if (empty($metrics)) {
            return ['score' => 0, 'status' => 'unknown'];
        }
        
        $totalScore = 0;
        $componentCount = 0;
        
        foreach ($metrics as $component => $data) {
            if (isset($data['health_score'])) {
                $totalScore += $data['health_score'];
                $componentCount++;
            }
        }
        
        $overallScore = $componentCount > 0 ? $totalScore / $componentCount : 0;
        
        $status = 'healthy';
        if ($overallScore < 50) {
            $status = 'critical';
        } elseif ($overallScore < 80) {
            $status = 'warning';
        }
        
        return [
            'score' => round($overallScore, 2),
            'status' => $status,
            'components_monitored' => $componentCount
        ];
    }
}
