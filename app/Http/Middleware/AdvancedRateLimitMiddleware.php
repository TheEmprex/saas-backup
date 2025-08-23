<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

class AdvancedRateLimitMiddleware
{
    /**
     * Rate limiting rules for different endpoint groups
     */
    protected $rateLimitRules = [
        'api.auth' => [
            'limit' => 10,
            'window' => 60, // 1 minute
            'by' => 'ip',
        ],
        'api.messaging' => [
            'limit' => 100,
            'window' => 60, // 1 minute
            'by' => 'user',
        ],
        'api.upload' => [
            'limit' => 20,
            'window' => 300, // 5 minutes
            'by' => 'user',
        ],
        'api.search' => [
            'limit' => 50,
            'window' => 60, // 1 minute
            'by' => 'user',
        ],
        'api.admin' => [
            'limit' => 200,
            'window' => 60, // 1 minute
            'by' => 'user',
        ],
        'default' => [
            'limit' => 60,
            'window' => 60, // 1 minute
            'by' => 'user',
        ],
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $group = 'default'): Response
    {
        $rule = $this->rateLimitRules[$group] ?? $this->rateLimitRules['default'];
        
        $key = $this->getRateLimitKey($request, $rule['by']);
        $limit = $rule['limit'];
        $window = $rule['window'];

        // Check if rate limit is exceeded
        if ($this->isRateLimitExceeded($key, $limit, $window)) {
            $this->logRateLimitExceeded($request, $key, $group);
            throw new TooManyRequestsHttpException($window, 'Too many requests');
        }

        // Increment the rate limit counter
        $this->incrementRateLimit($key, $window);

        $response = $next($request);

        // Add rate limit headers
        $this->addRateLimitHeaders($response, $key, $limit, $window);

        return $response;
    }

    /**
     * Generate rate limit key based on the strategy
     */
    protected function getRateLimitKey(Request $request, string $by): string
    {
        $prefix = 'rate_limit:';
        
        switch ($by) {
            case 'ip':
                return $prefix . 'ip:' . $request->ip();
            case 'user':
                return $prefix . 'user:' . ($request->user()?->id ?? $request->ip());
            case 'ip_and_user':
                return $prefix . 'ip_user:' . $request->ip() . ':' . ($request->user()?->id ?? 'guest');
            default:
                return $prefix . 'default:' . $request->ip();
        }
    }

    /**
     * Check if rate limit is exceeded
     */
    protected function isRateLimitExceeded(string $key, int $limit, int $window): bool
    {
        $current = Cache::get($key, 0);
        return $current >= $limit;
    }

    /**
     * Increment the rate limit counter
     */
    protected function incrementRateLimit(string $key, int $window): void
    {
        $current = Cache::get($key, 0);
        
        if ($current === 0) {
            // First request in the window
            Cache::put($key, 1, $window);
        } else {
            // Increment existing counter
            Cache::increment($key);
        }
    }

    /**
     * Add rate limit headers to response
     */
    protected function addRateLimitHeaders(Response $response, string $key, int $limit, int $window): void
    {
        $current = Cache::get($key, 0);
        $remaining = max(0, $limit - $current);
        $resetTime = Cache::get($key . ':reset') ?? now()->addSeconds($window)->timestamp;

        $response->headers->set('X-RateLimit-Limit', $limit);
        $response->headers->set('X-RateLimit-Remaining', $remaining);
        $response->headers->set('X-RateLimit-Reset', $resetTime);
        $response->headers->set('X-RateLimit-Window', $window);
    }

    /**
     * Log rate limit exceeded attempts
     */
    protected function logRateLimitExceeded(Request $request, string $key, string $group): void
    {
        logger()->warning('Rate limit exceeded', [
            'key' => $key,
            'group' => $group,
            'ip' => $request->ip(),
            'user_id' => $request->user()?->id,
            'user_agent' => $request->userAgent(),
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'timestamp' => now()->toISOString(),
        ]);

        // Track suspicious activity
        $this->trackSuspiciousActivity($request, $key);
    }

    /**
     * Track suspicious activity for potential attacks
     */
    protected function trackSuspiciousActivity(Request $request, string $key): void
    {
        $suspiciousKey = 'suspicious_activity:' . $request->ip();
        $count = Cache::get($suspiciousKey, 0);
        
        Cache::put($suspiciousKey, $count + 1, 3600); // Track for 1 hour

        // Alert if too many rate limit violations
        if ($count >= 10) {
            logger()->alert('Potential attack detected - multiple rate limit violations', [
                'ip' => $request->ip(),
                'violations' => $count,
                'user_agent' => $request->userAgent(),
                'timestamp' => now()->toISOString(),
            ]);

            // Consider temporarily blocking this IP
            $this->considerBlocking($request->ip());
        }
    }

    /**
     * Consider blocking an IP address
     */
    protected function considerBlocking(string $ip): void
    {
        $blockKey = 'blocked_ip:' . $ip;
        
        // Block for 1 hour
        Cache::put($blockKey, true, 3600);
        
        logger()->warning('IP address temporarily blocked due to suspicious activity', [
            'ip' => $ip,
            'blocked_until' => now()->addHour()->toISOString(),
        ]);
    }

    /**
     * Check if IP is blocked
     */
    public static function isBlocked(string $ip): bool
    {
        return Cache::has('blocked_ip:' . $ip);
    }

    /**
     * Get rate limit status for monitoring
     */
    public static function getRateLimitStatus(string $key): array
    {
        return [
            'current' => Cache::get($key, 0),
            'ttl' => Cache::ttl($key),
            'blocked' => Cache::has('blocked_ip:' . request()->ip()),
        ];
    }
}
