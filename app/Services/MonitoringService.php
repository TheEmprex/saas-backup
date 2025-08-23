<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class MonitoringService
{
    protected LoggingService $logger;
    protected array $metrics = [];

    public function __construct(LoggingService $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Collect comprehensive system health metrics
     */
    public function collectSystemHealth(): array
    {
        $health = [
            'timestamp' => Carbon::now()->toISOString(),
            'status' => 'healthy',
            'services' => [],
            'metrics' => []
        ];

        try {
            // Database health
            $health['services']['database'] = $this->checkDatabaseHealth();
            
            // Cache health
            $health['services']['cache'] = $this->checkCacheHealth();
            
            // Storage health
            $health['services']['storage'] = $this->checkStorageHealth();
            
            // Queue health
            $health['services']['queue'] = $this->checkQueueHealth();
            
            // System resources
            $health['metrics']['system'] = $this->getSystemMetrics();
            
            // Application metrics
            $health['metrics']['application'] = $this->getApplicationMetrics();
            
            // Performance metrics
            $health['metrics']['performance'] = $this->getPerformanceMetrics();
            
            // Determine overall status
            $health['status'] = $this->determineOverallHealth($health['services']);

        } catch (\Exception $e) {
            $health['status'] = 'unhealthy';
            $health['error'] = $e->getMessage();
            $this->logger->logError($e, ['context' => 'system_health_check']);
        }

        // Store for historical tracking
        $this->storeHealthMetrics($health);

        return $health;
    }

    /**
     * Check database connectivity and performance
     */
    protected function checkDatabaseHealth(): array
    {
        $startTime = microtime(true);
        
        try {
            // Test basic connectivity
            DB::select('SELECT 1');
            
            // Check query performance
            $queryTime = microtime(true) - $startTime;
            
            // Get connection info
            $connectionInfo = [
                'driver' => config('database.default'),
                'host' => config('database.connections.' . config('database.default') . '.host'),
                'database' => config('database.connections.' . config('database.default') . '.database')
            ];
            
            // Check slow queries
            $slowQueries = $this->getSlowQueries();
            
            // Check table sizes
            $tableSizes = $this->getTableSizes();
            
            return [
                'status' => 'healthy',
                'response_time' => round($queryTime * 1000, 2),
                'connection_info' => $connectionInfo,
                'slow_queries_count' => count($slowQueries),
                'largest_tables' => array_slice($tableSizes, 0, 5),
                'checked_at' => Carbon::now()->toISOString()
            ];
            
        } catch (\Exception $e) {
            return [
                'status' => 'unhealthy',
                'error' => $e->getMessage(),
                'checked_at' => Carbon::now()->toISOString()
            ];
        }
    }

    /**
     * Check cache system health
     */
    protected function checkCacheHealth(): array
    {
        $startTime = microtime(true);
        $testKey = 'health_check_' . uniqid();
        $testValue = 'test_' . time();
        
        try {
            // Test write
            Cache::put($testKey, $testValue, 60);
            
            // Test read
            $retrieved = Cache::get($testKey);
            
            // Test delete
            Cache::forget($testKey);
            
            $responseTime = microtime(true) - $startTime;
            
            if ($retrieved !== $testValue) {
                throw new \Exception('Cache read/write test failed');
            }
            
            // Get cache statistics
            $stats = $this->getCacheStatistics();
            
            return [
                'status' => 'healthy',
                'response_time' => round($responseTime * 1000, 2),
                'driver' => config('cache.default'),
                'statistics' => $stats,
                'checked_at' => Carbon::now()->toISOString()
            ];
            
        } catch (\Exception $e) {
            return [
                'status' => 'unhealthy',
                'error' => $e->getMessage(),
                'checked_at' => Carbon::now()->toISOString()
            ];
        }
    }

    /**
     * Check storage systems
     */
    protected function checkStorageHealth(): array
    {
        $storage = [];
        $disks = ['local', 'public'];
        
        foreach ($disks as $disk) {
            try {
                $testFile = 'health_check_' . uniqid() . '.txt';
                $testContent = 'health check ' . time();
                
                // Test write
                Storage::disk($disk)->put($testFile, $testContent);
                
                // Test read
                $retrieved = Storage::disk($disk)->get($testFile);
                
                // Test delete
                Storage::disk($disk)->delete($testFile);
                
                if ($retrieved !== $testContent) {
                    throw new \Exception('File read/write test failed');
                }
                
                // Get disk space info
                $path = Storage::disk($disk)->path('');
                $diskSpace = $this->getDiskSpaceInfo($path);
                
                $storage[$disk] = [
                    'status' => 'healthy',
                    'disk_space' => $diskSpace,
                    'checked_at' => Carbon::now()->toISOString()
                ];
                
            } catch (\Exception $e) {
                $storage[$disk] = [
                    'status' => 'unhealthy',
                    'error' => $e->getMessage(),
                    'checked_at' => Carbon::now()->toISOString()
                ];
            }
        }
        
        return $storage;
    }

    /**
     * Check queue system health
     */
    protected function checkQueueHealth(): array
    {
        try {
            // Get queue sizes
            $queueSizes = $this->getQueueSizes();
            
            // Get failed jobs count
            $failedJobs = DB::table('failed_jobs')->count();
            
            // Check for stuck jobs
            $stuckJobs = $this->getStuckJobs();
            
            return [
                'status' => count($stuckJobs) > 10 ? 'warning' : 'healthy',
                'queue_sizes' => $queueSizes,
                'failed_jobs' => $failedJobs,
                'stuck_jobs' => count($stuckJobs),
                'checked_at' => Carbon::now()->toISOString()
            ];
            
        } catch (\Exception $e) {
            return [
                'status' => 'unhealthy',
                'error' => $e->getMessage(),
                'checked_at' => Carbon::now()->toISOString()
            ];
        }
    }

    /**
     * Get system resource metrics
     */
    protected function getSystemMetrics(): array
    {
        return [
            'memory' => [
                'usage' => memory_get_usage(true),
                'peak' => memory_get_peak_usage(true),
                'limit' => $this->parseMemoryValue(ini_get('memory_limit'))
            ],
            'cpu' => [
                'load_average' => $this->getLoadAverage(),
                'processes' => $this->getProcessCount()
            ],
            'server' => [
                'hostname' => gethostname(),
                'php_version' => PHP_VERSION,
                'laravel_version' => app()->version(),
                'uptime' => $this->getSystemUptime()
            ],
            'timestamp' => Carbon::now()->toISOString()
        ];
    }

    /**
     * Get application-specific metrics
     */
    protected function getApplicationMetrics(): array
    {
        return [
            'users' => [
                'total' => DB::table('users')->count(),
                'active_today' => $this->getActiveUsersCount(1),
                'active_week' => $this->getActiveUsersCount(7),
                'new_today' => DB::table('users')
                    ->whereDate('created_at', Carbon::today())
                    ->count()
            ],
            'messages' => [
                'total' => DB::table('messages')->count(),
                'sent_today' => DB::table('messages')
                    ->whereDate('created_at', Carbon::today())
                    ->count(),
                'sent_hour' => DB::table('messages')
                    ->where('created_at', '>=', Carbon::now()->subHour())
                    ->count()
            ],
            'conversations' => [
                'total' => DB::table('conversations')->count(),
                'active_today' => $this->getActiveConversationsCount(1)
            ],
            'errors' => [
                'last_hour' => $this->getErrorCount(1),
                'last_24h' => $this->getErrorCount(24)
            ],
            'timestamp' => Carbon::now()->toISOString()
        ];
    }

    /**
     * Get performance metrics
     */
    protected function getPerformanceMetrics(): array
    {
        $cacheKey = 'performance_metrics:' . date('Y-m-d-H');
        $metrics = Cache::get($cacheKey, []);
        
        if (empty($metrics)) {
            return [
                'requests' => [],
                'average_response_time' => 0,
                'slow_requests_count' => 0,
                'timestamp' => Carbon::now()->toISOString()
            ];
        }
        
        $responseTimes = array_column($metrics, 'execution_time');
        $memoryUsages = array_column($metrics, 'memory_usage');
        $queryTimes = array_column($metrics, 'db_queries');
        
        return [
            'requests' => [
                'total' => count($metrics),
                'average_response_time' => round(array_sum($responseTimes) / count($responseTimes) * 1000, 2),
                'max_response_time' => round(max($responseTimes) * 1000, 2),
                'slow_requests_count' => count(array_filter($responseTimes, fn($t) => $t > 1)),
            ],
            'memory' => [
                'average_usage' => round(array_sum($memoryUsages) / count($memoryUsages)),
                'peak_usage' => max($memoryUsages)
            ],
            'database' => [
                'average_queries' => round(array_sum($queryTimes) / count($queryTimes)),
                'max_queries' => max($queryTimes)
            ],
            'timestamp' => Carbon::now()->toISOString()
        ];
    }

    /**
     * Store health metrics for historical analysis
     */
    protected function storeHealthMetrics(array $health): void
    {
        $cacheKey = 'health_metrics:' . date('Y-m-d-H-i');
        Cache::put($cacheKey, $health, 86400); // Store for 24 hours
        
        // Keep only essential data for long-term storage
        $compactMetrics = [
            'timestamp' => $health['timestamp'],
            'status' => $health['status'],
            'database_response_time' => $health['services']['database']['response_time'] ?? null,
            'cache_response_time' => $health['services']['cache']['response_time'] ?? null,
            'memory_usage' => $health['metrics']['system']['memory']['usage'] ?? null,
            'active_users' => $health['metrics']['application']['users']['active_today'] ?? null,
            'error_count' => $health['metrics']['application']['errors']['last_hour'] ?? null
        ];
        
        // Store in database for long-term trends
        try {
            DB::table('system_metrics')->insert([
                'timestamp' => Carbon::now(),
                'metrics' => json_encode($compactMetrics),
                'created_at' => Carbon::now()
            ]);
        } catch (\Exception $e) {
            // Silently fail to avoid recursive errors
            $this->logger->logError($e, ['context' => 'storing_health_metrics']);
        }
    }

    /**
     * Get historical health data
     */
    public function getHealthHistory(Carbon $from, Carbon $to): array
    {
        try {
            $history = DB::table('system_metrics')
                ->whereBetween('timestamp', [$from, $to])
                ->orderBy('timestamp', 'asc')
                ->get()
                ->map(function ($record) {
                    return array_merge(
                        json_decode($record->metrics, true),
                        ['id' => $record->id]
                    );
                })
                ->toArray();
                
            return $history;
        } catch (\Exception $e) {
            $this->logger->logError($e, ['context' => 'getting_health_history']);
            return [];
        }
    }

    /**
     * Get real-time alerts
     */
    public function getActiveAlerts(): array
    {
        $alerts = [];
        $health = $this->collectSystemHealth();
        
        // Check for unhealthy services
        foreach ($health['services'] as $service => $status) {
            if ($status['status'] === 'unhealthy') {
                $alerts[] = [
                    'type' => 'service_down',
                    'service' => $service,
                    'message' => "Service {$service} is unhealthy: " . ($status['error'] ?? 'Unknown error'),
                    'severity' => 'critical',
                    'timestamp' => Carbon::now()->toISOString()
                ];
            }
        }
        
        // Check performance thresholds
        $performance = $health['metrics']['performance'] ?? [];
        if (isset($performance['requests']['average_response_time']) && 
            $performance['requests']['average_response_time'] > 1000) {
            $alerts[] = [
                'type' => 'performance',
                'message' => "High average response time: {$performance['requests']['average_response_time']}ms",
                'severity' => 'warning',
                'timestamp' => Carbon::now()->toISOString()
            ];
        }
        
        // Check error rates
        $errorCount = $health['metrics']['application']['errors']['last_hour'] ?? 0;
        if ($errorCount > 50) {
            $alerts[] = [
                'type' => 'error_rate',
                'message' => "High error rate: {$errorCount} errors in the last hour",
                'severity' => 'warning',
                'timestamp' => Carbon::now()->toISOString()
            ];
        }
        
        // Check disk space
        foreach ($health['services']['storage'] as $disk => $info) {
            if (isset($info['disk_space']['percentage_used']) && 
                $info['disk_space']['percentage_used'] > 85) {
                $alerts[] = [
                    'type' => 'disk_space',
                    'message' => "Low disk space on {$disk}: {$info['disk_space']['percentage_used']}% used",
                    'severity' => 'warning',
                    'timestamp' => Carbon::now()->toISOString()
                ];
            }
        }
        
        return $alerts;
    }

    // Helper methods

    protected function determineOverallHealth(array $services): string
    {
        $statuses = array_column($services, 'status');
        
        if (in_array('unhealthy', $statuses)) {
            return 'unhealthy';
        } elseif (in_array('warning', $statuses)) {
            return 'warning';
        }
        
        return 'healthy';
    }

    protected function getSlowQueries(): array
    {
        // This would query your slow query log
        // Implementation depends on your database system
        return [];
    }

    protected function getTableSizes(): array
    {
        try {
            $sizes = DB::select("
                SELECT table_name, 
                       round(((data_length + index_length) / 1024 / 1024), 2) as size_mb
                FROM information_schema.tables 
                WHERE table_schema = ? 
                ORDER BY (data_length + index_length) DESC
            ", [config('database.connections.mysql.database')]);
            
            return array_map(function ($row) {
                return [
                    'table' => $row->table_name,
                    'size_mb' => $row->size_mb
                ];
            }, $sizes);
        } catch (\Exception $e) {
            return [];
        }
    }

    protected function getCacheStatistics(): array
    {
        // This would depend on your cache driver
        // For Redis, you could use Redis commands
        return [
            'driver' => config('cache.default'),
            'hit_ratio' => 0.85 // Placeholder
        ];
    }

    protected function getDiskSpaceInfo(string $path): array
    {
        try {
            $totalBytes = disk_total_space($path);
            $freeBytes = disk_free_space($path);
            $usedBytes = $totalBytes - $freeBytes;
            
            return [
                'total_gb' => round($totalBytes / 1024 / 1024 / 1024, 2),
                'used_gb' => round($usedBytes / 1024 / 1024 / 1024, 2),
                'free_gb' => round($freeBytes / 1024 / 1024 / 1024, 2),
                'percentage_used' => round(($usedBytes / $totalBytes) * 100, 2)
            ];
        } catch (\Exception $e) {
            return [];
        }
    }

    protected function getQueueSizes(): array
    {
        try {
            return [
                'default' => DB::table('jobs')->where('queue', 'default')->count(),
                'high' => DB::table('jobs')->where('queue', 'high')->count(),
                'low' => DB::table('jobs')->where('queue', 'low')->count()
            ];
        } catch (\Exception $e) {
            return [];
        }
    }

    protected function getStuckJobs(): array
    {
        try {
            return DB::table('jobs')
                ->where('created_at', '<', Carbon::now()->subHours(2))
                ->where('attempts', '>', 0)
                ->get()
                ->toArray();
        } catch (\Exception $e) {
            return [];
        }
    }

    protected function getLoadAverage(): array
    {
        if (function_exists('sys_getloadavg')) {
            $load = sys_getloadavg();
            return [
                '1_min' => $load[0],
                '5_min' => $load[1],
                '15_min' => $load[2]
            ];
        }
        
        return [];
    }

    protected function getProcessCount(): int
    {
        if (function_exists('shell_exec')) {
            $output = shell_exec('ps aux | wc -l');
            return (int) $output ?: 0;
        }
        
        return 0;
    }

    protected function getSystemUptime(): ?string
    {
        if (function_exists('shell_exec')) {
            return trim(shell_exec('uptime'));
        }
        
        return null;
    }

    protected function parseMemoryValue(string $value): int
    {
        $value = trim($value);
        $lastChar = strtolower($value[strlen($value) - 1]);
        $numeric = (int) $value;
        
        switch ($lastChar) {
            case 'g':
                return $numeric * 1024 * 1024 * 1024;
            case 'm':
                return $numeric * 1024 * 1024;
            case 'k':
                return $numeric * 1024;
            default:
                return $numeric;
        }
    }

    protected function getActiveUsersCount(int $days): int
    {
        return Cache::remember("active_users:{$days}", 300, function () use ($days) {
            $userIds = [];
            $cachePattern = "user_last_activity:*";
            
            // This is a simplified version - in production you'd want to use
            // a more efficient method to scan cache keys
            for ($i = 1; $i <= 1000; $i++) {
                $lastActivity = Cache::get("user_last_activity:{$i}");
                if ($lastActivity && Carbon::parse($lastActivity)->gte(Carbon::now()->subDays($days))) {
                    $userIds[] = $i;
                }
            }
            
            return count($userIds);
        });
    }

    protected function getActiveConversationsCount(int $days): int
    {
        return Cache::remember("active_conversations:{$days}", 300, function () use ($days) {
            return DB::table('conversations')
                ->join('messages', 'conversations.id', '=', 'messages.conversation_id')
                ->where('messages.created_at', '>=', Carbon::now()->subDays($days))
                ->distinct('conversations.id')
                ->count();
        });
    }

    protected function getErrorCount(int $hours): int
    {
        // Count cached error events
        $errorCount = 0;
        $cacheKeys = Cache::get('error_tracking_keys', []);
        
        foreach ($cacheKeys as $key) {
            $errorData = Cache::get($key);
            if ($errorData && isset($errorData['last_occurrence'])) {
                $lastOccurrence = Carbon::parse($errorData['last_occurrence']);
                if ($lastOccurrence->gte(Carbon::now()->subHours($hours))) {
                    $errorCount += $errorData['count'] ?? 1;
                }
            }
        }
        
        return $errorCount;
    }
}
