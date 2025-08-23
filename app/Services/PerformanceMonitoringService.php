<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class PerformanceMonitoringService
{
    private array $metrics = [];
    private float $startTime;
    private int $startMemory;

    public function __construct()
    {
        $this->startTime = microtime(true);
        $this->startMemory = memory_get_usage(true);
    }

    /**
     * Start monitoring a request
     */
    public function startRequest(Request $request): void
    {
        $this->metrics['request'] = [
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'user_id' => auth()->id(),
            'start_time' => $this->startTime,
            'start_memory' => $this->startMemory,
        ];
    }

    /**
     * End monitoring a request
     */
    public function endRequest(): array
    {
        $endTime = microtime(true);
        $endMemory = memory_get_usage(true);

        $this->metrics['performance'] = [
            'execution_time' => round(($endTime - $this->startTime) * 1000, 2), // milliseconds
            'memory_usage' => $this->formatBytes($endMemory - $this->startMemory),
            'peak_memory' => $this->formatBytes(memory_get_peak_usage(true)),
            'database_queries' => $this->getDatabaseQueryCount(),
        ];

        $this->storeMetrics();
        
        return $this->metrics;
    }

    /**
     * Monitor slow database queries
     */
    public function monitorSlowQueries(): void
    {
        DB::listen(function ($query) {
            if ($query->time > 1000) { // Queries taking more than 1 second
                Log::warning('Slow database query detected', [
                    'sql' => $query->sql,
                    'bindings' => $query->bindings,
                    'time' => $query->time . 'ms',
                    'connection' => $query->connectionName,
                    'user_id' => auth()->id(),
                    'url' => request()->fullUrl(),
                ]);
            }
        });
    }

    /**
     * Track API response times
     */
    public function trackApiResponse(string $endpoint, float $responseTime): void
    {
        $key = "api_response_times:{$endpoint}";
        $times = Cache::get($key, []);
        
        $times[] = [
            'time' => $responseTime,
            'timestamp' => now()->timestamp,
        ];
        
        // Keep only last 100 entries
        if (count($times) > 100) {
            array_shift($times);
        }
        
        Cache::put($key, $times, now()->addHours(24));
    }

    /**
     * Get API performance statistics
     */
    public function getApiStats(string $endpoint): array
    {
        $key = "api_response_times:{$endpoint}";
        $times = Cache::get($key, []);
        
        if (empty($times)) {
            return [];
        }
        
        $responseTimes = array_column($times, 'time');
        
        return [
            'endpoint' => $endpoint,
            'total_requests' => count($responseTimes),
            'average_response_time' => round(array_sum($responseTimes) / count($responseTimes), 2),
            'min_response_time' => min($responseTimes),
            'max_response_time' => max($responseTimes),
            'median_response_time' => $this->getMedian($responseTimes),
            'p95_response_time' => $this->getPercentile($responseTimes, 95),
            'p99_response_time' => $this->getPercentile($responseTimes, 99),
        ];
    }

    /**
     * Monitor memory usage
     */
    public function trackMemoryUsage(string $operation): void
    {
        $memoryUsage = memory_get_usage(true);
        $peakMemory = memory_get_peak_usage(true);
        
        Log::info('Memory usage tracking', [
            'operation' => $operation,
            'memory_usage' => $this->formatBytes($memoryUsage),
            'peak_memory' => $this->formatBytes($peakMemory),
            'user_id' => auth()->id(),
        ]);
    }

    /**
     * Track cache performance
     */
    public function trackCachePerformance(): array
    {
        $cacheService = app(CachingService::class);
        return $cacheService->getCacheStats();
    }

    /**
     * Monitor application errors
     */
    public function trackError(\Exception $exception, string $context = ''): void
    {
        $errorKey = 'app_errors:' . date('Y-m-d-H');
        $errors = Cache::get($errorKey, []);
        
        $errorData = [
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'context' => $context,
            'user_id' => auth()->id(),
            'url' => request()->fullUrl(),
            'timestamp' => now()->toISOString(),
        ];
        
        $errors[] = $errorData;
        
        // Keep only last 1000 errors per hour
        if (count($errors) > 1000) {
            array_shift($errors);
        }
        
        Cache::put($errorKey, $errors, now()->addHours(24));
        
        // Log critical errors immediately
        if ($this->isCriticalError($exception)) {
            Log::critical('Critical application error', $errorData);
        }
    }

    /**
     * Get system performance overview
     */
    public function getSystemOverview(): array
    {
        return [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time'),
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'database_connections' => $this->getDatabaseConnectionCount(),
            'cache_driver' => config('cache.default'),
            'queue_driver' => config('queue.default'),
            'session_driver' => config('session.driver'),
            'app_environment' => config('app.env'),
            'debug_mode' => config('app.debug'),
        ];
    }

    /**
     * Generate performance report
     */
    public function generatePerformanceReport(string $period = '24h'): array
    {
        $endTime = now();
        $startTime = $this->getPeriodStartTime($period);
        
        return [
            'period' => $period,
            'start_time' => $startTime->toISOString(),
            'end_time' => $endTime->toISOString(),
            'total_requests' => $this->getTotalRequests($startTime, $endTime),
            'average_response_time' => $this->getAverageResponseTime($startTime, $endTime),
            'error_rate' => $this->getErrorRate($startTime, $endTime),
            'slowest_endpoints' => $this->getSlowestEndpoints($startTime, $endTime),
            'most_active_users' => $this->getMostActiveUsers($startTime, $endTime),
            'system_overview' => $this->getSystemOverview(),
            'cache_performance' => $this->trackCachePerformance(),
        ];
    }

    /**
     * Alert on performance thresholds
     */
    public function checkPerformanceThresholds(): array
    {
        $alerts = [];
        
        // Check average response time
        $avgResponseTime = $this->getAverageResponseTime(now()->subMinutes(5), now());
        if ($avgResponseTime > 2000) { // 2 seconds
            $alerts[] = [
                'type' => 'slow_response',
                'message' => "Average response time is {$avgResponseTime}ms (threshold: 2000ms)",
                'severity' => 'warning',
            ];
        }
        
        // Check error rate
        $errorRate = $this->getErrorRate(now()->subMinutes(10), now());
        if ($errorRate > 5) { // 5% error rate
            $alerts[] = [
                'type' => 'high_error_rate',
                'message' => "Error rate is {$errorRate}% (threshold: 5%)",
                'severity' => 'critical',
            ];
        }
        
        // Check memory usage
        $memoryUsage = memory_get_usage(true);
        $memoryLimit = $this->parseMemoryLimit(ini_get('memory_limit'));
        $memoryPercentage = ($memoryUsage / $memoryLimit) * 100;
        
        if ($memoryPercentage > 80) {
            $alerts[] = [
                'type' => 'high_memory_usage',
                'message' => "Memory usage is {$memoryPercentage}% (threshold: 80%)",
                'severity' => 'warning',
            ];
        }
        
        return $alerts;
    }

    /**
     * Private helper methods
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $power = $bytes > 0 ? floor(log($bytes, 1024)) : 0;
        return number_format($bytes / (1024 ** $power), 2, '.', ',') . ' ' . $units[$power];
    }

    private function getDatabaseQueryCount(): int
    {
        return count(DB::getQueryLog());
    }

    private function getDatabaseConnectionCount(): int
    {
        try {
            return DB::connection()->getPdo() ? 1 : 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function getMedian(array $values): float
    {
        sort($values);
        $count = count($values);
        $middle = floor(($count - 1) / 2);
        
        if ($count % 2) {
            return $values[$middle];
        }
        
        return ($values[$middle] + $values[$middle + 1]) / 2;
    }

    private function getPercentile(array $values, int $percentile): float
    {
        sort($values);
        $index = ($percentile / 100) * (count($values) - 1);
        
        if (floor($index) == $index) {
            return $values[$index];
        }
        
        $lower = $values[floor($index)];
        $upper = $values[ceil($index)];
        return $lower + ($upper - $lower) * ($index - floor($index));
    }

    private function isCriticalError(\Exception $exception): bool
    {
        $criticalErrors = [
            \Error::class,
            \ParseError::class,
            \TypeError::class,
        ];
        
        foreach ($criticalErrors as $errorClass) {
            if ($exception instanceof $errorClass) {
                return true;
            }
        }
        
        return false;
    }

    private function storeMetrics(): void
    {
        $key = 'performance_metrics:' . date('Y-m-d-H');
        $metrics = Cache::get($key, []);
        $metrics[] = $this->metrics;
        
        Cache::put($key, $metrics, now()->addDays(7));
    }

    private function getPeriodStartTime(string $period): \Carbon\Carbon
    {
        return match ($period) {
            '1h' => now()->subHour(),
            '6h' => now()->subHours(6),
            '24h' => now()->subDay(),
            '7d' => now()->subWeek(),
            '30d' => now()->subMonth(),
            default => now()->subDay(),
        };
    }

    private function getTotalRequests(\Carbon\Carbon $start, \Carbon\Carbon $end): int
    {
        // Implementation depends on your request logging mechanism
        return 0;
    }

    private function getAverageResponseTime(\Carbon\Carbon $start, \Carbon\Carbon $end): float
    {
        // Implementation depends on your metrics storage
        return 0.0;
    }

    private function getErrorRate(\Carbon\Carbon $start, \Carbon\Carbon $end): float
    {
        // Implementation depends on your error logging mechanism
        return 0.0;
    }

    private function getSlowestEndpoints(\Carbon\Carbon $start, \Carbon\Carbon $end): array
    {
        // Implementation depends on your metrics storage
        return [];
    }

    private function getMostActiveUsers(\Carbon\Carbon $start, \Carbon\Carbon $end): array
    {
        // Implementation depends on your user activity tracking
        return [];
    }

    private function parseMemoryLimit(string $memoryLimit): int
    {
        $value = (int) $memoryLimit;
        $unit = strtolower(substr($memoryLimit, -1));
        
        return match ($unit) {
            'g' => $value * 1024 * 1024 * 1024,
            'm' => $value * 1024 * 1024,
            'k' => $value * 1024,
            default => $value,
        };
    }
}
