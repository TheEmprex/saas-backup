<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Request;
use Illuminate\Http\Request as HttpRequest;
use Carbon\Carbon;
use Exception;

class LoggingService
{
    protected array $logChannels = [
        'security' => 'security',
        'performance' => 'performance',
        'api' => 'api',
        'messaging' => 'messaging',
        'user_activity' => 'user_activity',
        'system' => 'system',
        'error' => 'error',
        'audit' => 'audit'
    ];

    protected array $logLevels = [
        'emergency' => 0,
        'alert' => 1,
        'critical' => 2,
        'error' => 3,
        'warning' => 4,
        'notice' => 5,
        'info' => 6,
        'debug' => 7
    ];

    /**
     * Log security events
     */
    public function logSecurity(string $event, array $context = [], string $level = 'info'): void
    {
        $logData = [
            'event' => $event,
            'timestamp' => Carbon::now()->toISOString(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'user_id' => auth()->id(),
            'session_id' => session()->getId(),
            'context' => $context,
            'severity' => $level
        ];

        Log::channel('security')->{$level}($event, $logData);

        // Also store in cache for real-time monitoring
        $this->storeLiveEvent('security', $logData);

        // Alert on critical security events
        if (in_array($level, ['emergency', 'alert', 'critical', 'error'])) {
            $this->triggerSecurityAlert($event, $logData);
        }
    }

    /**
     * Log performance metrics
     */
    public function logPerformance(string $operation, array $metrics = [], string $level = 'info'): void
    {
        $logData = [
            'operation' => $operation,
            'timestamp' => Carbon::now()->toISOString(),
            'user_id' => auth()->id(),
            'metrics' => array_merge([
                'execution_time' => microtime(true) - LARAVEL_START,
                'memory_usage' => memory_get_usage(true),
                'memory_peak' => memory_get_peak_usage(true),
                'db_queries' => $this->getDatabaseQueryCount(),
            ], $metrics),
            'request_data' => [
                'method' => request()->method(),
                'url' => request()->fullUrl(),
                'route' => optional(request()->route())->getName(),
            ]
        ];

        Log::channel('performance')->{$level}($operation, $logData);

        // Store metrics for analysis
        $this->storePerformanceMetric($operation, $logData['metrics']);
    }

    /**
     * Log API requests and responses
     */
    public function logApiRequest(HttpRequest $request, $response = null, array $context = []): void
    {
        $logData = [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'route' => optional($request->route())->getName(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'user_id' => auth()->id(),
            'request_id' => $request->header('X-Request-ID') ?: uniqid(),
            'timestamp' => Carbon::now()->toISOString(),
            'request_size' => strlen($request->getContent()),
            'context' => $context
        ];

        if ($response) {
            $logData['response'] = [
                'status_code' => $response->getStatusCode(),
                'response_size' => strlen($response->getContent()),
                'headers' => array_intersect_key(
                    $response->headers->all(),
                    array_flip(['content-type', 'cache-control', 'x-ratelimit-remaining'])
                )
            ];
        }

        $level = $this->determineLogLevel($response ? $response->getStatusCode() : 200);
        Log::channel('api')->{$level}('API Request', $logData);
    }

    /**
     * Log messaging events
     */
    public function logMessaging(string $event, array $context = []): void
    {
        $logData = [
            'event' => $event,
            'timestamp' => Carbon::now()->toISOString(),
            'user_id' => auth()->id(),
            'context' => $context
        ];

        Log::channel('messaging')->info($event, $logData);
    }

    /**
     * Log user activity
     */
    public function logUserActivity(string $action, array $context = []): void
    {
        if (!auth()->check()) {
            return;
        }

        $logData = [
            'action' => $action,
            'user_id' => auth()->id(),
            'user_email' => auth()->user()->email,
            'timestamp' => Carbon::now()->toISOString(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'session_id' => session()->getId(),
            'context' => $context
        ];

        Log::channel('user_activity')->info($action, $logData);

        // Update user's last activity
        $this->updateUserLastActivity();
    }

    /**
     * Log system events
     */
    public function logSystem(string $event, array $context = [], string $level = 'info'): void
    {
        $logData = [
            'event' => $event,
            'timestamp' => Carbon::now()->toISOString(),
            'server' => gethostname(),
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'context' => $context
        ];

        Log::channel('system')->{$level}($event, $logData);
    }

    /**
     * Log errors with full context
     */
    public function logError(Exception $exception, array $context = []): void
    {
        $logData = [
            'exception' => get_class($exception),
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
            'timestamp' => Carbon::now()->toISOString(),
            'user_id' => auth()->id(),
            'url' => request()->fullUrl(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'context' => $context,
            'server_info' => [
                'php_version' => PHP_VERSION,
                'memory_usage' => memory_get_usage(true),
                'memory_limit' => ini_get('memory_limit')
            ]
        ];

        Log::channel('error')->error('Application Error', $logData);

        // Store for error tracking
        $this->storeErrorForTracking($exception, $logData);
    }

    /**
     * Log audit trail
     */
    public function logAudit(string $action, $model = null, array $changes = [], array $context = []): void
    {
        $logData = [
            'action' => $action,
            'user_id' => auth()->id(),
            'user_email' => auth()->user()?->email,
            'timestamp' => Carbon::now()->toISOString(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'context' => $context
        ];

        if ($model) {
            $logData['model'] = [
                'type' => get_class($model),
                'id' => $model->getKey(),
                'attributes' => $model->getAttributes()
            ];
        }

        if (!empty($changes)) {
            $logData['changes'] = $changes;
        }

        Log::channel('audit')->info($action, $logData);
    }

    /**
     * Get log summary for dashboard
     */
    public function getLogSummary(Carbon $from, Carbon $to): array
    {
        $summary = [];

        foreach ($this->logChannels as $channel) {
            $summary[$channel] = [
                'total_entries' => $this->getLogCount($channel, $from, $to),
                'error_count' => $this->getLogCount($channel, $from, $to, ['error', 'critical', 'emergency']),
                'warning_count' => $this->getLogCount($channel, $from, $to, ['warning']),
                'recent_entries' => $this->getRecentLogEntries($channel, 5)
            ];
        }

        return $summary;
    }

    /**
     * Search logs
     */
    public function searchLogs(array $criteria): array
    {
        $results = [];
        
        foreach ($criteria['channels'] ?? $this->logChannels as $channel) {
            $logFiles = $this->getLogFiles($channel);
            
            foreach ($logFiles as $file) {
                $entries = $this->searchInLogFile($file, $criteria);
                $results = array_merge($results, $entries);
            }
        }

        return $this->sortLogEntries($results);
    }

    /**
     * Export logs
     */
    public function exportLogs(array $criteria, string $format = 'json'): string
    {
        $logs = $this->searchLogs($criteria);

        switch ($format) {
            case 'csv':
                return $this->exportToCSV($logs);
            case 'json':
                return json_encode($logs, JSON_PRETTY_PRINT);
            case 'xml':
                return $this->exportToXML($logs);
            default:
                throw new Exception("Unsupported export format: {$format}");
        }
    }

    /**
     * Clean old logs
     */
    public function cleanOldLogs(int $daysToKeep = 30): int
    {
        $deletedCount = 0;
        $cutoffDate = Carbon::now()->subDays($daysToKeep);

        foreach ($this->logChannels as $channel) {
            $logFiles = $this->getLogFiles($channel);
            
            foreach ($logFiles as $file) {
                $fileDate = $this->extractDateFromLogFileName($file);
                
                if ($fileDate && $fileDate->lt($cutoffDate)) {
                    Storage::disk('logs')->delete($file);
                    $deletedCount++;
                }
            }
        }

        return $deletedCount;
    }

    /**
     * Get real-time log events
     */
    public function getLiveEvents(string $channel = null, int $limit = 100): array
    {
        $cacheKey = $channel ? "live_logs:{$channel}" : 'live_logs:all';
        return Cache::get($cacheKey, []);
    }

    // Protected helper methods

    protected function storeLiveEvent(string $channel, array $data): void
    {
        $cacheKey = "live_logs:{$channel}";
        $events = Cache::get($cacheKey, []);
        
        array_unshift($events, $data);
        $events = array_slice($events, 0, 100); // Keep only last 100 events
        
        Cache::put($cacheKey, $events, 3600); // Store for 1 hour
    }

    protected function triggerSecurityAlert(string $event, array $data): void
    {
        // This could send notifications, emails, or trigger webhooks
        $alertData = [
            'type' => 'security_alert',
            'event' => $event,
            'severity' => $data['severity'],
            'timestamp' => $data['timestamp'],
            'user_id' => $data['user_id'],
            'ip_address' => $data['ip_address']
        ];

        // Store alert for admin dashboard
        Cache::put("security_alert:" . uniqid(), $alertData, 86400);
        
        // You could integrate with services like Slack, Discord, etc.
        // $this->sendSlackAlert($alertData);
    }

    protected function getDatabaseQueryCount(): int
    {
        return count(\DB::getQueryLog());
    }

    protected function storePerformanceMetric(string $operation, array $metrics): void
    {
        $cacheKey = "performance_metrics:{$operation}:" . date('Y-m-d-H');
        $storedMetrics = Cache::get($cacheKey, []);
        
        $storedMetrics[] = $metrics;
        
        // Keep only last 1000 metrics per hour
        if (count($storedMetrics) > 1000) {
            $storedMetrics = array_slice($storedMetrics, -1000);
        }
        
        Cache::put($cacheKey, $storedMetrics, 3600);
    }

    protected function updateUserLastActivity(): void
    {
        if (auth()->check()) {
            Cache::put(
                "user_last_activity:" . auth()->id(),
                Carbon::now()->toISOString(),
                86400
            );
        }
    }

    protected function storeErrorForTracking(Exception $exception, array $logData): void
    {
        $errorHash = md5($exception->getFile() . $exception->getLine() . $exception->getMessage());
        $cacheKey = "error_tracking:{$errorHash}";
        
        $errorData = Cache::get($cacheKey, [
            'first_occurrence' => $logData['timestamp'],
            'count' => 0,
            'last_occurrence' => null,
            'exception_class' => get_class($exception),
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine()
        ]);
        
        $errorData['count']++;
        $errorData['last_occurrence'] = $logData['timestamp'];
        
        Cache::put($cacheKey, $errorData, 86400);
    }

    protected function determineLogLevel(int $statusCode): string
    {
        if ($statusCode >= 500) {
            return 'error';
        } elseif ($statusCode >= 400) {
            return 'warning';
        } else {
            return 'info';
        }
    }

    protected function getLogCount(string $channel, Carbon $from, Carbon $to, array $levels = null): int
    {
        // This would need to be implemented based on your log storage
        // For now, return a placeholder
        return rand(10, 1000);
    }

    protected function getRecentLogEntries(string $channel, int $limit): array
    {
        // This would fetch recent entries from log files
        // For now, return empty array
        return [];
    }

    protected function getLogFiles(string $channel): array
    {
        return Storage::disk('logs')->files($channel) ?: [];
    }

    protected function searchInLogFile(string $file, array $criteria): array
    {
        // This would parse log files and search for matching entries
        // Implementation depends on log format and storage
        return [];
    }

    protected function sortLogEntries(array $entries): array
    {
        usort($entries, function ($a, $b) {
            return strtotime($b['timestamp']) - strtotime($a['timestamp']);
        });

        return $entries;
    }

    protected function exportToCSV(array $logs): string
    {
        if (empty($logs)) {
            return '';
        }

        $output = fopen('php://temp', 'r+');
        
        // Write headers
        fputcsv($output, array_keys($logs[0]));
        
        // Write data
        foreach ($logs as $log) {
            fputcsv($output, $log);
        }
        
        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);
        
        return $csv;
    }

    protected function exportToXML(array $logs): string
    {
        $xml = new \SimpleXMLElement('<logs/>');
        
        foreach ($logs as $log) {
            $logEntry = $xml->addChild('entry');
            foreach ($log as $key => $value) {
                if (is_array($value)) {
                    $value = json_encode($value);
                }
                $logEntry->addChild($key, htmlspecialchars($value));
            }
        }
        
        return $xml->asXML();
    }

    protected function extractDateFromLogFileName(string $fileName): ?Carbon
    {
        // Extract date from filename like "laravel-2024-01-15.log"
        if (preg_match('/(\d{4}-\d{2}-\d{2})/', $fileName, $matches)) {
            return Carbon::parse($matches[1]);
        }
        
        return null;
    }
}
