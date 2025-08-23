<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class ApiSecurityMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Add security headers
        $response = $next($request);
        
        $this->addSecurityHeaders($response);
        $this->logSuspiciousActivity($request);
        $this->enforceApiRateLimit($request);
        
        return $response;
    }

    /**
     * Add security headers to the response
     */
    private function addSecurityHeaders($response): void
    {
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        $response->headers->set('Content-Security-Policy', "default-src 'self'");
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');
    }

    /**
     * Log suspicious API activity
     */
    private function logSuspiciousActivity(Request $request): void
    {
        $suspiciousPatterns = [
            '/\.\.\//i',  // Path traversal
            '/union\s+select/i',  // SQL injection
            '/<script/i',  // XSS
            '/eval\s*\(/i',  // Code injection
            '/base64_decode/i',  // Encoding attacks
        ];

        $requestData = $request->all();
        $requestString = json_encode($requestData);

        foreach ($suspiciousPatterns as $pattern) {
            if (preg_match($pattern, $requestString)) {
                Log::warning('Suspicious API request detected', [
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'url' => $request->fullUrl(),
                    'method' => $request->method(),
                    'pattern_matched' => $pattern,
                    'user_id' => auth()->id(),
                    'timestamp' => now()->toISOString(),
                ]);
                break;
            }
        }
    }

    /**
     * Enforce API rate limiting
     */
    private function enforceApiRateLimit(Request $request): void
    {
        $key = 'api_requests:' . $request->ip();
        $maxRequests = 100; // per minute
        $currentRequests = Cache::get($key, 0);

        if ($currentRequests >= $maxRequests) {
            Log::warning('API rate limit exceeded', [
                'ip' => $request->ip(),
                'requests' => $currentRequests,
                'limit' => $maxRequests,
                'user_id' => auth()->id(),
            ]);
        }

        Cache::put($key, $currentRequests + 1, now()->addMinute());
    }
}
