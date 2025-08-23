<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\BaseController;
use App\Services\LoggingService;
use App\Services\MonitoringService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class SystemController extends BaseController
{
    protected MonitoringService $monitoring;

    public function __construct(LoggingService $logger, MonitoringService $monitoring)
    {
        parent::__construct($logger);
        $this->monitoring = $monitoring;
        
        // Only allow admin users to access system endpoints
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->isAdmin()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied. Admin privileges required.'
                ], 403);
            }
            return $next($request);
        });
    }

    /**
     * Get system health status
     */
    public function health(): JsonResponse
    {
        try {
            $health = $this->monitoring->collectSystemHealth();

            $this->logActivity('system_health_checked', [
                'overall_status' => $health['status'],
                'services_count' => count($health['services'])
            ]);

            return $this->successResponse($health, 'System health retrieved successfully');

        } catch (\Exception $e) {
            return $this->handleException($e, 'checking_system_health');
        }
    }

    /**
     * Get system metrics and performance data
     */
    public function metrics(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'from' => 'nullable|date|before_or_equal:to',
                'to' => 'nullable|date|after_or_equal:from',
                'type' => 'nullable|in:performance,usage,errors,all'
            ]);

            if ($validator->fails()) {
                return $this->validationErrorResponse($validator->errors());
            }

            $from = $request->from ? Carbon::parse($request->from) : now()->subHours(24);
            $to = $request->to ? Carbon::parse($request->to) : now();
            $type = $request->get('type', 'all');

            $metrics = [
                'period' => [
                    'from' => $from->toISOString(),
                    'to' => $to->toISOString(),
                ],
                'data' => []
            ];

            // Get current health for immediate metrics
            $currentHealth = $this->monitoring->collectSystemHealth();
            $metrics['current'] = $currentHealth;

            // Get historical data
            $historicalData = $this->monitoring->getHealthHistory($from, $to);
            $metrics['history'] = $historicalData;

            // Get active alerts
            $alerts = $this->monitoring->getActiveAlerts();
            $metrics['alerts'] = $alerts;

            // Calculate aggregated metrics
            if (!empty($historicalData)) {
                $metrics['aggregated'] = [
                    'average_response_time' => $this->calculateAverageResponseTime($historicalData),
                    'error_rate' => $this->calculateErrorRate($historicalData),
                    'uptime_percentage' => $this->calculateUptimePercentage($historicalData),
                    'peak_memory_usage' => $this->calculatePeakMemoryUsage($historicalData)
                ];
            }

            $this->logActivity('system_metrics_retrieved', [
                'type' => $type,
                'from' => $from->toDateString(),
                'to' => $to->toDateString(),
                'data_points' => count($historicalData)
            ]);

            return $this->successResponse($metrics, 'System metrics retrieved successfully');

        } catch (\Exception $e) {
            return $this->handleException($e, 'getting_system_metrics');
        }
    }

    /**
     * Get system logs
     */
    public function logs(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'channel' => 'nullable|in:security,performance,api,messaging,user_activity,system,error,audit',
                'level' => 'nullable|in:emergency,alert,critical,error,warning,notice,info,debug',
                'from' => 'nullable|date|before_or_equal:to',
                'to' => 'nullable|date|after_or_equal:from',
                'per_page' => 'integer|min:1|max:100',
                'search' => 'nullable|string|max:255'
            ]);

            if ($validator->fails()) {
                return $this->validationErrorResponse($validator->errors());
            }

            $criteria = [
                'channels' => $request->channel ? [$request->channel] : null,
                'levels' => $request->level ? [$request->level] : null,
                'from' => $request->from ? Carbon::parse($request->from) : now()->subDays(7),
                'to' => $request->to ? Carbon::parse($request->to) : now(),
                'search' => $request->search,
                'per_page' => $request->get('per_page', 50)
            ];

            $logs = $this->logger->searchLogs($criteria);

            // Paginate results manually
            $perPage = $criteria['per_page'];
            $currentPage = $request->get('page', 1);
            $total = count($logs);
            $offset = ($currentPage - 1) * $perPage;
            $paginatedLogs = array_slice($logs, $offset, $perPage);

            $response = [
                'data' => $paginatedLogs,
                'meta' => [
                    'current_page' => $currentPage,
                    'per_page' => $perPage,
                    'total' => $total,
                    'last_page' => ceil($total / $perPage),
                    'from' => $offset + 1,
                    'to' => min($offset + $perPage, $total)
                ],
                'criteria' => $criteria
            ];

            $this->logActivity('system_logs_retrieved', [
                'channel' => $request->channel,
                'level' => $request->level,
                'total_logs' => $total
            ]);

            return $this->successResponse($response, 'System logs retrieved successfully');

        } catch (\Exception $e) {
            return $this->handleException($e, 'getting_system_logs');
        }
    }

    /**
     * Export logs
     */
    public function exportLogs(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'channel' => 'nullable|in:security,performance,api,messaging,user_activity,system,error,audit',
                'level' => 'nullable|in:emergency,alert,critical,error,warning,notice,info,debug',
                'from' => 'nullable|date|before_or_equal:to',
                'to' => 'nullable|date|after_or_equal:from',
                'format' => 'required|in:json,csv,xml',
                'search' => 'nullable|string|max:255'
            ]);

            if ($validator->fails()) {
                return $this->validationErrorResponse($validator->errors());
            }

            $criteria = [
                'channels' => $request->channel ? [$request->channel] : null,
                'levels' => $request->level ? [$request->level] : null,
                'from' => $request->from ? Carbon::parse($request->from) : now()->subDays(7),
                'to' => $request->to ? Carbon::parse($request->to) : now(),
                'search' => $request->search
            ];

            $format = $request->format;
            $exportData = $this->logger->exportLogs($criteria, $format);

            $filename = 'system_logs_' . now()->format('Y-m-d_H-i-s') . '.' . $format;

            $this->logActivity('system_logs_exported', [
                'format' => $format,
                'channel' => $request->channel,
                'level' => $request->level
            ]);

            return response($exportData, 200, [
                'Content-Type' => $this->getContentType($format),
                'Content-Disposition' => "attachment; filename=\"{$filename}\""
            ]);

        } catch (\Exception $e) {
            return $this->handleException($e, 'exporting_system_logs');
        }
    }

    /**
     * Get log summary for dashboard
     */
    public function getLogSummary(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'from' => 'nullable|date|before_or_equal:to',
                'to' => 'nullable|date|after_or_equal:from'
            ]);

            if ($validator->fails()) {
                return $this->validationErrorResponse($validator->errors());
            }

            $from = $request->from ? Carbon::parse($request->from) : now()->subDays(7);
            $to = $request->to ? Carbon::parse($request->to) : now();

            $summary = $this->logger->getLogSummary($from, $to);

            return $this->successResponse($summary, 'Log summary retrieved successfully');

        } catch (\Exception $e) {
            return $this->handleException($e, 'getting_log_summary');
        }
    }

    /**
     * Clean old logs
     */
    public function cleanOldLogs(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'days_to_keep' => 'integer|min:1|max:365'
            ]);

            if ($validator->fails()) {
                return $this->validationErrorResponse($validator->errors());
            }

            $daysToKeep = $request->get('days_to_keep', 30);
            $deletedCount = $this->logger->cleanOldLogs($daysToKeep);

            $this->logActivity('old_logs_cleaned', [
                'days_to_keep' => $daysToKeep,
                'deleted_count' => $deletedCount
            ]);

            $this->logAudit('old_logs_cleaned', null, [
                'days_to_keep' => $daysToKeep,
                'deleted_count' => $deletedCount
            ]);

            return $this->successResponse([
                'deleted_count' => $deletedCount,
                'days_kept' => $daysToKeep
            ], "Cleaned {$deletedCount} old log files");

        } catch (\Exception $e) {
            return $this->handleException($e, 'cleaning_old_logs');
        }
    }

    /**
     * Get real-time events
     */
    public function getLiveEvents(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'channel' => 'nullable|in:security,performance,api,messaging,user_activity,system,error,audit',
                'limit' => 'integer|min:1|max:500'
            ]);

            if ($validator->fails()) {
                return $this->validationErrorResponse($validator->errors());
            }

            $channel = $request->channel;
            $limit = $request->get('limit', 100);

            $events = $this->logger->getLiveEvents($channel, $limit);

            return $this->successResponse([
                'events' => $events,
                'channel' => $channel,
                'count' => count($events),
                'timestamp' => now()->toISOString()
            ], 'Live events retrieved successfully');

        } catch (\Exception $e) {
            return $this->handleException($e, 'getting_live_events');
        }
    }

    // Protected helper methods

    protected function getContentType(string $format): string
    {
        return match($format) {
            'json' => 'application/json',
            'csv' => 'text/csv',
            'xml' => 'application/xml',
            default => 'text/plain'
        };
    }

    protected function calculateAverageResponseTime(array $data): float
    {
        $responseTimes = array_column($data, 'database_response_time');
        $validTimes = array_filter($responseTimes, fn($time) => $time !== null);
        
        return !empty($validTimes) ? round(array_sum($validTimes) / count($validTimes), 2) : 0;
    }

    protected function calculateErrorRate(array $data): float
    {
        $errorCounts = array_column($data, 'error_count');
        $validCounts = array_filter($errorCounts, fn($count) => $count !== null);
        
        if (empty($validCounts)) {
            return 0;
        }

        $totalErrors = array_sum($validCounts);
        $dataPoints = count($validCounts);
        
        return $dataPoints > 0 ? round(($totalErrors / $dataPoints) * 100, 2) : 0;
    }

    protected function calculateUptimePercentage(array $data): float
    {
        $healthyCount = 0;
        $totalCount = count($data);
        
        foreach ($data as $point) {
            if (isset($point['status']) && $point['status'] === 'healthy') {
                $healthyCount++;
            }
        }
        
        return $totalCount > 0 ? round(($healthyCount / $totalCount) * 100, 2) : 100;
    }

    protected function calculatePeakMemoryUsage(array $data): int
    {
        $memoryUsages = array_column($data, 'memory_usage');
        $validUsages = array_filter($memoryUsages, fn($usage) => $usage !== null);
        
        return !empty($validUsages) ? max($validUsages) : 0;
    }
}
