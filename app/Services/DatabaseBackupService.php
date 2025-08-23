<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;
use Carbon\Carbon;

class DatabaseBackupService
{
    private string $backupPath;
    private string $backupDisk;
    private int $maxBackups;
    
    public function __construct()
    {
        $this->backupPath = 'database-backups';
        $this->backupDisk = config('backup.disk', 'local');
        $this->maxBackups = config('backup.max_backups', 30);
    }
    
    /**
     * Create a complete database backup
     */
    public function createBackup(string $type = 'manual'): array
    {
        $startTime = microtime(true);
        $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
        $filename = "backup_{$type}_{$timestamp}.sql";
        $filePath = $this->backupPath . '/' . $filename;
        
        try {
            Log::info('Starting database backup', [
                'type' => $type,
                'filename' => $filename
            ]);
            
            // Create backup directory if it doesn't exist
            if (!Storage::disk($this->backupDisk)->exists($this->backupPath)) {
                Storage::disk($this->backupDisk)->makeDirectory($this->backupPath);
            }
            
            // Generate SQL dump
            $sqlDump = $this->generateSqlDump();
            
            // Store the backup
            Storage::disk($this->backupDisk)->put($filePath, $sqlDump);
            
            // Compress the backup
            $compressedFile = $this->compressBackup($filePath);
            
            $duration = round((microtime(true) - $startTime) * 1000, 2);
            $fileSize = Storage::disk($this->backupDisk)->size($compressedFile);
            
            $backupInfo = [
                'success' => true,
                'filename' => basename($compressedFile),
                'file_path' => $compressedFile,
                'size' => $this->formatBytes($fileSize),
                'size_bytes' => $fileSize,
                'duration_ms' => $duration,
                'type' => $type,
                'created_at' => Carbon::now()->toISOString(),
                'tables_backed_up' => $this->getTableCount()
            ];
            
            Log::info('Database backup completed successfully', $backupInfo);
            
            // Clean up old backups
            $this->cleanupOldBackups();
            
            // Store backup metadata
            $this->storeBackupMetadata($backupInfo);
            
            return $backupInfo;
            
        } catch (\Exception $e) {
            Log::error('Database backup failed', [
                'error' => $e->getMessage(),
                'type' => $type,
                'filename' => $filename,
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'type' => $type,
                'filename' => $filename
            ];
        }
    }
    
    /**
     * Generate SQL dump of the database
     */
    private function generateSqlDump(): string
    {
        $config = config('database.connections.' . config('database.default'));
        
        $host = $config['host'];
        $database = $config['database'];
        $username = $config['username'];
        $password = $config['password'];
        $port = $config['port'] ?? 3306;
        
        // Build mysqldump command
        $command = sprintf(
            'mysqldump --single-transaction --routines --triggers --host=%s --port=%d --user=%s --password=%s %s',
            escapeshellarg($host),
            $port,
            escapeshellarg($username),
            escapeshellarg($password),
            escapeshellarg($database)
        );
        
        // Add timestamp and metadata
        $header = $this->generateBackupHeader();
        
        // Execute mysqldump
        $output = shell_exec($command);
        
        if ($output === null) {
            throw new \Exception('Failed to execute mysqldump. Make sure mysqldump is installed and accessible.');
        }
        
        return $header . $output;
    }
    
    /**
     * Generate backup header with metadata
     */
    private function generateBackupHeader(): string
    {
        $appName = config('app.name');
        $timestamp = Carbon::now()->toISOString();
        $version = app()->version();
        $environment = app()->environment();
        
        return "-- {$appName} Database Backup\n" .
               "-- Generated on: {$timestamp}\n" .
               "-- Laravel Version: {$version}\n" .
               "-- Environment: {$environment}\n" .
               "-- Database: " . config('database.connections.' . config('database.default'))['database'] . "\n" .
               "-- Backup Type: Full Database Dump\n" .
               "-- \n\n";
    }
    
    /**
     * Compress backup file using gzip
     */
    private function compressBackup(string $filePath): string
    {
        $compressedPath = $filePath . '.gz';
        
        // Read the original file
        $content = Storage::disk($this->backupDisk)->get($filePath);
        
        // Compress using gzip
        $compressedContent = gzencode($content, 9); // Maximum compression
        
        // Store compressed file
        Storage::disk($this->backupDisk)->put($compressedPath, $compressedContent);
        
        // Delete original uncompressed file
        Storage::disk($this->backupDisk)->delete($filePath);
        
        return $compressedPath;
    }
    
    /**
     * Clean up old backup files
     */
    private function cleanupOldBackups(): void
    {
        try {
            $backupFiles = Storage::disk($this->backupDisk)->files($this->backupPath);
            
            // Filter only .sql.gz files
            $backupFiles = array_filter($backupFiles, function ($file) {
                return str_ends_with($file, '.sql.gz');
            });
            
            // Sort by modification time (newest first)
            usort($backupFiles, function ($a, $b) {
                return Storage::disk($this->backupDisk)->lastModified($b) - 
                       Storage::disk($this->backupDisk)->lastModified($a);
            });
            
            // Delete old backups beyond the retention limit
            if (count($backupFiles) > $this->maxBackups) {
                $filesToDelete = array_slice($backupFiles, $this->maxBackups);
                
                foreach ($filesToDelete as $file) {
                    Storage::disk($this->backupDisk)->delete($file);
                    Log::info('Deleted old backup file', ['file' => $file]);
                }
            }
            
        } catch (\Exception $e) {
            Log::warning('Failed to cleanup old backups', ['error' => $e->getMessage()]);
        }
    }
    
    /**
     * Store backup metadata for tracking
     */
    private function storeBackupMetadata(array $backupInfo): void
    {
        try {
            $metadataFile = $this->backupPath . '/backup_metadata.json';
            
            // Get existing metadata
            $metadata = [];
            if (Storage::disk($this->backupDisk)->exists($metadataFile)) {
                $metadata = json_decode(
                    Storage::disk($this->backupDisk)->get($metadataFile),
                    true
                ) ?? [];
            }
            
            // Add new backup info
            $metadata[] = $backupInfo;
            
            // Keep only recent entries (last 100 backups)
            $metadata = array_slice($metadata, -100);
            
            // Store updated metadata
            Storage::disk($this->backupDisk)->put(
                $metadataFile,
                json_encode($metadata, JSON_PRETTY_PRINT)
            );
            
        } catch (\Exception $e) {
            Log::warning('Failed to store backup metadata', ['error' => $e->getMessage()]);
        }
    }
    
    /**
     * Restore database from backup
     */
    public function restoreBackup(string $backupFile): array
    {
        $startTime = microtime(true);
        
        try {
            Log::info('Starting database restore', ['backup_file' => $backupFile]);
            
            $filePath = $this->backupPath . '/' . $backupFile;
            
            if (!Storage::disk($this->backupDisk)->exists($filePath)) {
                throw new \Exception("Backup file not found: {$backupFile}");
            }
            
            // Read and decompress backup content
            $compressedContent = Storage::disk($this->backupDisk)->get($filePath);
            $sqlContent = gzdecode($compressedContent);
            
            if ($sqlContent === false) {
                throw new \Exception('Failed to decompress backup file');
            }
            
            // Execute SQL restore
            $this->executeSqlRestore($sqlContent);
            
            $duration = round((microtime(true) - $startTime) * 1000, 2);
            
            $restoreInfo = [
                'success' => true,
                'backup_file' => $backupFile,
                'duration_ms' => $duration,
                'restored_at' => Carbon::now()->toISOString()
            ];
            
            Log::info('Database restore completed successfully', $restoreInfo);
            
            return $restoreInfo;
            
        } catch (\Exception $e) {
            Log::error('Database restore failed', [
                'error' => $e->getMessage(),
                'backup_file' => $backupFile,
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'backup_file' => $backupFile
            ];
        }
    }
    
    /**
     * Execute SQL restore
     */
    private function executeSqlRestore(string $sqlContent): void
    {
        // Split SQL content into individual statements
        $statements = array_filter(
            preg_split('/;\s*$/m', $sqlContent),
            function ($statement) {
                return trim($statement) !== '' && !str_starts_with(trim($statement), '--');
            }
        );
        
        DB::transaction(function () use ($statements) {
            foreach ($statements as $statement) {
                $statement = trim($statement);
                if (!empty($statement)) {
                    DB::unprepared($statement);
                }
            }
        });
    }
    
    /**
     * Get list of available backups
     */
    public function getAvailableBackups(): array
    {
        try {
            $backupFiles = Storage::disk($this->backupDisk)->files($this->backupPath);
            
            // Filter only .sql.gz files
            $backupFiles = array_filter($backupFiles, function ($file) {
                return str_ends_with($file, '.sql.gz');
            });
            
            $backups = [];
            foreach ($backupFiles as $file) {
                $filename = basename($file);
                $size = Storage::disk($this->backupDisk)->size($file);
                $lastModified = Storage::disk($this->backupDisk)->lastModified($file);
                
                $backups[] = [
                    'filename' => $filename,
                    'file_path' => $file,
                    'size' => $this->formatBytes($size),
                    'size_bytes' => $size,
                    'created_at' => Carbon::createFromTimestamp($lastModified)->toISOString(),
                    'age' => Carbon::createFromTimestamp($lastModified)->diffForHumans()
                ];
            }
            
            // Sort by creation time (newest first)
            usort($backups, function ($a, $b) {
                return strtotime($b['created_at']) - strtotime($a['created_at']);
            });
            
            return $backups;
            
        } catch (\Exception $e) {
            Log::error('Failed to get available backups', ['error' => $e->getMessage()]);
            return [];
        }
    }
    
    /**
     * Get backup statistics
     */
    public function getBackupStats(): array
    {
        try {
            $backups = $this->getAvailableBackups();
            
            $totalSize = array_sum(array_column($backups, 'size_bytes'));
            $totalCount = count($backups);
            
            $latest = $totalCount > 0 ? $backups[0] : null;
            $oldest = $totalCount > 0 ? end($backups) : null;
            
            // Get backup frequency (backups per day)
            $frequency = 0;
            if ($totalCount > 1 && $oldest && $latest) {
                $daysDiff = Carbon::parse($latest['created_at'])->diffInDays(Carbon::parse($oldest['created_at']));
                $frequency = $daysDiff > 0 ? round($totalCount / $daysDiff, 2) : $totalCount;
            }
            
            return [
                'total_backups' => $totalCount,
                'total_size' => $this->formatBytes($totalSize),
                'total_size_bytes' => $totalSize,
                'latest_backup' => $latest ? $latest['created_at'] : null,
                'oldest_backup' => $oldest ? $oldest['created_at'] : null,
                'backup_frequency_per_day' => $frequency,
                'disk_used' => $this->backupDisk,
                'retention_days' => $this->maxBackups
            ];
            
        } catch (\Exception $e) {
            Log::error('Failed to get backup stats', ['error' => $e->getMessage()]);
            return [];
        }
    }
    
    /**
     * Verify backup integrity
     */
    public function verifyBackup(string $backupFile): array
    {
        try {
            $filePath = $this->backupPath . '/' . $backupFile;
            
            if (!Storage::disk($this->backupDisk)->exists($filePath)) {
                throw new \Exception("Backup file not found: {$backupFile}");
            }
            
            // Test decompression
            $compressedContent = Storage::disk($this->backupDisk)->get($filePath);
            $sqlContent = gzdecode($compressedContent);
            
            if ($sqlContent === false) {
                throw new \Exception('Failed to decompress backup file');
            }
            
            // Basic SQL syntax check
            $hasCreateTable = strpos($sqlContent, 'CREATE TABLE') !== false;
            $hasInsert = strpos($sqlContent, 'INSERT INTO') !== false;
            $hasHeader = strpos($sqlContent, 'Database Backup') !== false;
            
            return [
                'success' => true,
                'backup_file' => $backupFile,
                'can_decompress' => true,
                'has_create_statements' => $hasCreateTable,
                'has_insert_statements' => $hasInsert,
                'has_backup_header' => $hasHeader,
                'content_size' => strlen($sqlContent),
                'verified_at' => Carbon::now()->toISOString()
            ];
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'backup_file' => $backupFile,
                'error' => $e->getMessage(),
                'verified_at' => Carbon::now()->toISOString()
            ];
        }
    }
    
    /**
     * Get number of tables in database
     */
    private function getTableCount(): int
    {
        try {
            $tables = DB::select('SHOW TABLES');
            return count($tables);
        } catch (\Exception $e) {
            return 0;
        }
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
}
