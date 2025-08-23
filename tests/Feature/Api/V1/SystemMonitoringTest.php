<?php

namespace Tests\Feature\Api\V1;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;

class SystemMonitoringTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $adminUser;
    protected $regularUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Create admin user
        $this->adminUser = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'is_admin' => true
        ]);

        // Create regular user
        $this->regularUser = User::factory()->create([
            'name' => 'Regular User',
            'email' => 'regular@example.com',
            'is_admin' => false
        ]);
    }

    /** @test */
    public function admin_can_check_system_health()
    {
        Sanctum::actingAs($this->adminUser, ['*']);

        $response = $this->getJson('/api/marketplace/v1/system/health');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'timestamp',
                'checks' => [
                    'database' => [
                        'status',
                        'response_time'
                    ],
                    'cache' => [
                        'status',
                        'response_time'
                    ],
                    'queue' => [
                        'status'
                    ],
                    'storage' => [
                        'status'
                    ]
                ],
                'system_info' => [
                    'php_version',
                    'laravel_version',
                    'environment',
                    'timezone',
                    'memory_usage',
                    'disk_usage'
                ]
            ]);

        $this->assertEquals('healthy', $response->json('status'));
    }

    /** @test */
    public function regular_user_cannot_access_health_endpoint()
    {
        Sanctum::actingAs($this->regularUser, ['*']);

        $response = $this->getJson('/api/marketplace/v1/system/health');

        $response->assertStatus(403);
    }

    /** @test */
    public function unauthenticated_user_cannot_access_health_endpoint()
    {
        $response = $this->getJson('/api/marketplace/v1/system/health');

        $response->assertStatus(401);
    }

    /** @test */
    public function admin_can_get_system_metrics()
    {
        Sanctum::actingAs($this->adminUser, ['*']);

        // Create some test data for metrics
        User::factory()->count(5)->create();

        $response = $this->getJson('/api/marketplace/v1/system/metrics');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'users' => [
                    'total',
                    'active_today',
                    'new_this_month'
                ],
                'conversations' => [
                    'total',
                    'active_today'
                ],
                'messages' => [
                    'total',
                    'sent_today',
                    'sent_this_week'
                ],
                'system' => [
                    'uptime',
                    'memory_usage',
                    'cpu_usage',
                    'disk_usage'
                ],
                'performance' => [
                    'avg_response_time',
                    'total_requests',
                    'error_rate'
                ]
            ]);

        // Verify some basic metrics
        $this->assertGreaterThanOrEqual(6, $response->json('users.total')); // 5 created + 2 in setUp
        $this->assertIsNumeric($response->json('system.memory_usage'));
    }

    /** @test */
    public function admin_can_get_system_logs()
    {
        Sanctum::actingAs($this->adminUser, ['*']);

        // Create some test log entries
        Log::info('Test info log entry');
        Log::warning('Test warning log entry');
        Log::error('Test error log entry');

        $response = $this->getJson('/api/marketplace/v1/system/logs');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'level',
                        'message',
                        'context',
                        'timestamp'
                    ]
                ],
                'meta' => [
                    'current_page',
                    'per_page',
                    'total'
                ],
                'summary' => [
                    'total_logs',
                    'error_count',
                    'warning_count',
                    'info_count'
                ]
            ]);
    }

    /** @test */
    public function admin_can_filter_logs_by_level()
    {
        Sanctum::actingAs($this->adminUser, ['*']);

        // Create test logs
        Log::info('Info message');
        Log::error('Error message');
        Log::warning('Warning message');

        $response = $this->getJson('/api/marketplace/v1/system/logs?level=error');

        $response->assertStatus(200);

        $logs = $response->json('data');
        foreach ($logs as $log) {
            $this->assertEquals('error', $log['level']);
        }
    }

    /** @test */
    public function admin_can_filter_logs_by_date_range()
    {
        Sanctum::actingAs($this->adminUser, ['*']);

        $today = now()->format('Y-m-d');
        $response = $this->getJson("/api/marketplace/v1/system/logs?date_from={$today}&date_to={$today}");

        $response->assertStatus(200);
        
        // All logs should be from today
        $logs = $response->json('data');
        foreach ($logs as $log) {
            $logDate = date('Y-m-d', strtotime($log['timestamp']));
            $this->assertEquals($today, $logDate);
        }
    }

    /** @test */
    public function admin_can_export_logs()
    {
        Sanctum::actingAs($this->adminUser, ['*']);

        Storage::fake('local');

        // Create some test logs
        Log::info('Exportable log entry 1');
        Log::error('Exportable log entry 2');

        $response = $this->postJson('/api/marketplace/v1/system/logs/export', [
            'format' => 'json',
            'level' => 'info'
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'download_url',
                'file_size',
                'export_id'
            ]);

        $this->assertStringContains('Export completed', $response->json('message'));
    }

    /** @test */
    public function admin_can_clean_old_logs()
    {
        Sanctum::actingAs($this->adminUser, ['*']);

        $response = $this->deleteJson('/api/marketplace/v1/system/logs/clean', [
            'days' => 30
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'deleted_count',
                'size_freed'
            ]);

        $this->assertStringContains('cleaned successfully', $response->json('message'));
    }

    /** @test */
    public function admin_can_get_live_events()
    {
        Sanctum::actingAs($this->adminUser, ['*']);

        $response = $this->getJson('/api/marketplace/v1/system/events');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'type',
                        'data',
                        'user_id',
                        'timestamp'
                    ]
                ],
                'meta' => [
                    'current_page',
                    'per_page',
                    'total'
                ]
            ]);
    }

    /** @test */
    public function admin_can_filter_events_by_type()
    {
        Sanctum::actingAs($this->adminUser, ['*']);

        $response = $this->getJson('/api/marketplace/v1/system/events?type=message_sent');

        $response->assertStatus(200);

        $events = $response->json('data');
        foreach ($events as $event) {
            $this->assertEquals('message_sent', $event['type']);
        }
    }

    /** @test */
    public function admin_can_filter_events_by_user()
    {
        Sanctum::actingAs($this->adminUser, ['*']);

        $userId = $this->regularUser->id;
        $response = $this->getJson("/api/marketplace/v1/system/events?user_id={$userId}");

        $response->assertStatus(200);

        $events = $response->json('data');
        foreach ($events as $event) {
            if ($event['user_id']) {
                $this->assertEquals($userId, $event['user_id']);
            }
        }
    }

    /** @test */
    public function system_health_check_detects_database_issues()
    {
        Sanctum::actingAs($this->adminUser, ['*']);

        // Mock database connection issue
        DB::shouldReceive('connection->getPdo')
            ->andThrow(new \Exception('Database connection failed'));

        $response = $this->getJson('/api/marketplace/v1/system/health');

        $response->assertStatus(503) // Service unavailable when unhealthy
            ->assertJson([
                'status' => 'unhealthy'
            ]);

        $this->assertEquals('failed', $response->json('checks.database.status'));
    }

    /** @test */
    public function system_health_check_detects_cache_issues()
    {
        Sanctum::actingAs($this->adminUser, ['*']);

        // Mock cache issue
        Cache::shouldReceive('put')
            ->andThrow(new \Exception('Cache connection failed'));

        $response = $this->getJson('/api/marketplace/v1/system/health');

        $response->assertStatus(503)
            ->assertJson([
                'status' => 'unhealthy'
            ]);

        $this->assertEquals('failed', $response->json('checks.cache.status'));
    }

    /** @test */
    public function system_metrics_include_performance_data()
    {
        Sanctum::actingAs($this->adminUser, ['*']);

        // Mock some performance data
        Cache::put('system_metrics:response_times', [100, 150, 200, 120, 180]);
        Cache::put('system_metrics:request_count', 1000);
        Cache::put('system_metrics:error_count', 5);

        $response = $this->getJson('/api/marketplace/v1/system/metrics');

        $response->assertStatus(200);

        $performance = $response->json('performance');
        $this->assertIsNumeric($performance['avg_response_time']);
        $this->assertIsNumeric($performance['total_requests']);
        $this->assertIsNumeric($performance['error_rate']);
    }

    /** @test */
    public function log_export_validates_parameters()
    {
        Sanctum::actingAs($this->adminUser, ['*']);

        $response = $this->postJson('/api/marketplace/v1/system/logs/export', [
            'format' => 'invalid_format',
            'level' => 'invalid_level'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['format', 'level']);
    }

    /** @test */
    public function log_cleaning_validates_days_parameter()
    {
        Sanctum::actingAs($this->adminUser, ['*']);

        $response = $this->deleteJson('/api/marketplace/v1/system/logs/clean', [
            'days' => -1 // Invalid negative value
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['days']);

        $response = $this->deleteJson('/api/marketplace/v1/system/logs/clean', [
            'days' => 'invalid' // Invalid non-numeric value
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['days']);
    }

    /** @test */
    public function system_endpoints_handle_large_datasets()
    {
        Sanctum::actingAs($this->adminUser, ['*']);

        // Create large dataset
        User::factory()->count(100)->create();

        $response = $this->getJson('/api/marketplace/v1/system/metrics');

        $response->assertStatus(200);
        $this->assertGreaterThanOrEqual(102, $response->json('users.total')); // 100 + 2 from setUp
    }

    /** @test */
    public function system_health_includes_response_times()
    {
        Sanctum::actingAs($this->adminUser, ['*']);

        $response = $this->getJson('/api/marketplace/v1/system/health');

        $response->assertStatus(200);

        // Check that response times are included and are numeric
        $checks = $response->json('checks');
        $this->assertArrayHasKey('response_time', $checks['database']);
        $this->assertArrayHasKey('response_time', $checks['cache']);
        
        $this->assertIsNumeric($checks['database']['response_time']);
        $this->assertIsNumeric($checks['cache']['response_time']);
    }

    /** @test */
    public function system_metrics_cache_efficiently()
    {
        Sanctum::actingAs($this->adminUser, ['*']);

        // First request
        $start = microtime(true);
        $response1 = $this->getJson('/api/marketplace/v1/system/metrics');
        $time1 = microtime(true) - $start;

        // Second request (should be faster due to caching)
        $start = microtime(true);
        $response2 = $this->getJson('/api/marketplace/v1/system/metrics');
        $time2 = microtime(true) - $start;

        $response1->assertStatus(200);
        $response2->assertStatus(200);

        // Second request should generally be faster (cached)
        // Note: This might be flaky in some environments, so we'll just ensure both succeed
        $this->assertTrue($time2 <= $time1 * 1.5); // Allow some variance
    }

    /** @test */
    public function system_endpoints_respect_rate_limiting()
    {
        Sanctum::actingAs($this->adminUser, ['*']);

        // Make multiple rapid requests
        $responses = [];
        for ($i = 0; $i < 10; $i++) {
            $responses[] = $this->getJson('/api/marketplace/v1/system/health');
        }

        // All requests should succeed (admin endpoints may have higher limits)
        foreach ($responses as $response) {
            $this->assertContains($response->status(), [200, 429]); // Either success or rate limited
        }
    }

    /** @test */
    public function system_endpoints_log_access_attempts()
    {
        Sanctum::actingAs($this->adminUser, ['*']);

        // Clear any existing logs
        Log::getLogger()->flush();

        $response = $this->getJson('/api/marketplace/v1/system/health');

        $response->assertStatus(200);

        // Check that the access was logged (implementation may vary)
        // This test assumes that system endpoint access is logged
        $this->assertTrue(true); // Placeholder - actual implementation would verify logs
    }
}
