<?php

namespace App\Console\Commands;

use App\Services\PerformanceMonitorService;
use App\Services\DatabaseBackupService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class MonitorProduction extends Command
{
    protected $signature = 'production:monitor {--backup : Create database backup}';
    protected $description = 'Monitor production system health and performance';
    
    private PerformanceMonitorService $performanceMonitor;
    private DatabaseBackupService $backupService;
    
    public function __construct(
        PerformanceMonitorService $performanceMonitor,
        DatabaseBackupService $backupService
    ) {
        parent::__construct();
        $this->performanceMonitor = $performanceMonitor;
        $this->backupService = $backupService;
    }
    
    public function handle(): int
    {
        $this->info('🔍 Starting Production System Monitoring...');
        
        // Monitor system performance
        $this->monitorSystemHealth();
        
        // Create backup if requested
        if ($this->option('backup')) {
            $this->createScheduledBackup();
        }
        
        // Clean up old logs
        $this->cleanupLogs();
        
        // Check disk space
        $this->checkDiskSpace();
        
        // Monitor application metrics
        $this->monitorApplicationMetrics();
        
        $this->info('✅ Production monitoring completed successfully');
        
        return self::SUCCESS;
    }
    
    private function monitorSystemHealth(): void
    {
        $this->info('📊 Monitoring system health...');
        
        try {
            $metrics = $this->performanceMonitor->monitorPerformance();
            $overallHealth = $this->performanceMonitor->getOverallHealthScore();
            
            $this->table(
                ['Component', 'Status', 'Health Score', 'Details'],
                collect($metrics)->map(function ($data, $component) {
                    return [
                        ucfirst($component),
                        $data['status'] ?? 'unknown',
                        isset($data['health_score']) ? round($data['health_score'], 1) . '%' : 'N/A',
                        $this->formatDetails($data)
                    ];
                })->toArray()
            );
            
            $statusColor = $overallHealth['status'] === 'healthy' ? 'green' : 
                          ($overallHealth['status'] === 'warning' ? 'yellow' : 'red');
            
            $this->line(sprintf(
                '<fg=%s>Overall Health: %s (%.1f%%)</fg=%s>',
                $statusColor,
                strtoupper($overallHealth['status']),
                $overallHealth['score'],
                $statusColor
            ));
            
            // Send alerts if health is poor
            if ($overallHealth['score'] < 80) {
                $this->sendHealthAlert($metrics, $overallHealth);
            }
            
        } catch (\Exception $e) {
            $this->error('Failed to monitor system health: ' . $e->getMessage());
            Log::error('System health monitoring failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
    
    private function createScheduledBackup(): void
    {
        $this->info('💾 Creating scheduled database backup...');
        
        try {
            $result = $this->backupService->createBackup('scheduled');
            
            if ($result['success']) {
                $this->info(sprintf(
                    '✅ Backup created: %s (%s, %s)',
                    $result['filename'],
                    $result['size'],
                    $result['duration_ms'] . 'ms'
                ));
            } else {
                $this->error('❌ Backup failed: ' . $result['error']);
            }
            
        } catch (\Exception $e) {
            $this->error('Failed to create backup: ' . $e->getMessage());
            Log::error('Scheduled backup failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
    
    private function cleanupLogs(): void
    {
        $this->info('🧹 Cleaning up old log files...');
        
        try {
            $logPath = storage_path('logs');
            $files = glob($logPath . '/laravel-*.log');
            $deleted = 0;
            $totalSize = 0;
            
            // Keep only last 30 days of log files
            $cutoffTime = time() - (30 * 24 * 60 * 60);
            
            foreach ($files as $file) {
                if (filemtime($file) < $cutoffTime) {
                    $size = filesize($file);
                    if (unlink($file)) {
                        $deleted++;
                        $totalSize += $size;
                    }
                }
            }
            
            if ($deleted > 0) {
                $this->info(sprintf(
                    '🗑️  Deleted %d old log files, freed %s',
                    $deleted,
                    $this->formatBytes($totalSize)
                ));
            } else {
                $this->info('✅ No old log files to clean up');
            }
            
        } catch (\Exception $e) {
            $this->warn('Failed to cleanup logs: ' . $e->getMessage());
        }
    }
    
    private function checkDiskSpace(): void
    {
        $this->info('💽 Checking disk space...');
        
        try {
            $paths = [
                'Storage' => storage_path(),
                'Public' => public_path(),
                'Logs' => storage_path('logs')
            ];
            
            $diskInfo = [];
            
            foreach ($paths as $name => $path) {
                $freeBytes = disk_free_space($path);
                $totalBytes = disk_total_space($path);
                $usedBytes = $totalBytes - $freeBytes;
                $usedPercent = ($usedBytes / $totalBytes) * 100;
                
                $diskInfo[] = [
                    $name,
                    $this->formatBytes($usedBytes),
                    $this->formatBytes($freeBytes),
                    $this->formatBytes($totalBytes),
                    round($usedPercent, 1) . '%'
                ];
                
                // Alert if disk usage is high
                if ($usedPercent > 85) {
                    $this->warn(sprintf(
                        '⚠️  High disk usage on %s: %.1f%% used',
                        $name,
                        $usedPercent
                    ));
                }
            }
            
            $this->table(
                ['Path', 'Used', 'Free', 'Total', 'Usage'],
                $diskInfo
            );
            
        } catch (\Exception $e) {
            $this->warn('Failed to check disk space: ' . $e->getMessage());
        }
    }
    
    private function monitorApplicationMetrics(): void
    {
        $this->info('📈 Monitoring application metrics...');
        
        try {
            // Get application-specific metrics
            $metrics = [
                'Users' => \App\Models\User::count(),
                'Messages' => \App\Models\Message::count(),
                'Job Posts' => \App\Models\JobPost::count(),
                'Applications' => \App\Models\JobApplication::count(),
                'Active Sessions' => $this->getActiveSessionCount(),
                'Failed Jobs' => \DB::table('failed_jobs')->count(),
            ];
            
            $this->table(
                ['Metric', 'Count'],
                collect($metrics)->map(function ($count, $metric) {
                    return [$metric, number_format($count)];
                })->toArray()
            );
            
            // Check for anomalies
            $this->checkMetricAnomalies($metrics);
            
        } catch (\Exception $e) {
            $this->warn('Failed to monitor application metrics: ' . $e->getMessage());
        }
    }
    
    private function getActiveSessionCount(): int
    {
        try {
            return \DB::table('sessions')->count();
        } catch (\Exception $e) {
            return 0;
        }
    }
    
    private function checkMetricAnomalies(array $metrics): void
    {
        // Alert on high failed job count
        if ($metrics['Failed Jobs'] > 100) {
            $this->warn(sprintf(
                '⚠️  High failed job count: %d failed jobs',
                $metrics['Failed Jobs']
            ));
        }
        
        // Alert on zero active sessions (might indicate issues)
        if ($metrics['Active Sessions'] === 0) {
            $this->warn('⚠️  No active sessions found - possible session issue');
        }
    }
    
    private function sendHealthAlert(array $metrics, array $overallHealth): void
    {
        try {
            $alertData = [
                'timestamp' => now()->toISOString(),
                'overall_health' => $overallHealth,
                'metrics' => $metrics,
                'server' => gethostname(),
                'environment' => app()->environment()
            ];
            
            Log::warning('Production health alert triggered', $alertData);
            
            // You can implement email/Slack notifications here
            // Example: Mail::to('admin@example.com')->send(new HealthAlert($alertData));
            
        } catch (\Exception $e) {
            Log::error('Failed to send health alert', ['error' => $e->getMessage()]);
        }
    }
    
    private function formatDetails(array $data): string
    {
        $details = [];
        
        if (isset($data['response_time'])) {
            $details[] = $data['response_time'] . 'ms';
        }
        
        if (isset($data['connection_time'])) {
            $details[] = 'conn: ' . $data['connection_time'] . 'ms';
        }
        
        if (isset($data['usage_percentage'])) {
            $details[] = $data['usage_percentage'] . '% used';
        }
        
        if (isset($data['failed_jobs'])) {
            $details[] = $data['failed_jobs'] . ' failed';
        }
        
        if (isset($data['error'])) {
            $details[] = 'ERROR: ' . $data['error'];
        }
        
        return implode(', ', $details) ?: 'OK';
    }
    
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes >= 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }
}
