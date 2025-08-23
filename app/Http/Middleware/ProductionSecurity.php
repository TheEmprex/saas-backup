<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class ProductionSecurity
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Security headers for production
        $this->addSecurityHeaders($request);
        
        // Advanced rate limiting
        $this->handleRateLimiting($request);
        
        // Log suspicious activities
        $this->logSuspiciousActivity($request);
        
        // Block malicious requests
        if ($this->isBlockedRequest($request)) {
            Log::warning('Blocked suspicious request', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'url' => $request->fullUrl(),
                'method' => $request->method()
            ]);
            
            abort(403, 'Request blocked for security reasons');
        }
        
        return $next($request);
    }
    
    /**
     * Add security headers to the response
     */
    private function addSecurityHeaders(Request $request): void
    {
        // Force HTTPS in production
        if (app()->environment('production') && !$request->secure()) {
            $secureUrl = str_replace('http://', 'https://', $request->fullUrl());
            redirect($secureUrl, 301)->send();
            exit;
        }
    }
    
    /**
     * Handle advanced rate limiting based on request type
     */
    private function handleRateLimiting(Request $request): void
    {
        $key = $this->getRateLimitKey($request);
        $maxAttempts = $this->getMaxAttempts($request);
        $decayMinutes = $this->getDecayMinutes($request);
        
        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            Log::warning('Rate limit exceeded', [
                'ip' => $request->ip(),
                'key' => $key,
                'attempts' => RateLimiter::attempts($key)
            ]);
            
            abort(429, 'Too many requests');
        }
        
        RateLimiter::hit($key, $decayMinutes * 60);
    }
    
    /**
     * Get rate limit key based on request characteristics
     */
    private function getRateLimitKey(Request $request): string
    {
        $baseKey = $request->ip();
        
        // Different keys for different endpoints
        if ($request->is('api/*')) {
            return "api:{$baseKey}";
        }
        
        if ($request->is('login') || $request->is('register')) {
            return "auth:{$baseKey}";
        }
        
        if ($request->is('messages/*') || $request->is('api/messages/*')) {
            return "messaging:{$baseKey}";
        }
        
        return "general:{$baseKey}";
    }
    
    /**
     * Get max attempts based on request type
     */
    private function getMaxAttempts(Request $request): int
    {
        if ($request->is('login') || $request->is('register')) {
            return 5; // Stricter for auth endpoints
        }
        
        if ($request->is('api/*')) {
            return 100; // More lenient for API
        }
        
        if ($request->is('messages/*')) {
            return 50; // Moderate for messaging
        }
        
        return 60; // Default rate limit
    }
    
    /**
     * Get decay minutes based on request type
     */
    private function getDecayMinutes(Request $request): int
    {
        if ($request->is('login') || $request->is('register')) {
            return 15; // Longer cooldown for auth
        }
        
        return 1; // Standard 1-minute window
    }
    
    /**
     * Log suspicious activities
     */
    private function logSuspiciousActivity(Request $request): void
    {
        $suspiciousPatterns = [
            '/\.\.\//i', // Path traversal
            '/union.*select/i', // SQL injection
            '/<script/i', // XSS attempts
            '/eval\(/i', // Code injection
            '/base64_decode/i', // Base64 injection
        ];
        
        $requestData = json_encode([
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'input' => $request->all(),
            'headers' => $request->headers->all()
        ]);
        
        foreach ($suspiciousPatterns as $pattern) {
            if (preg_match($pattern, $requestData)) {
                Log::warning('Suspicious request detected', [
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'pattern' => $pattern,
                    'request_data' => $requestData
                ]);
                break;
            }
        }
    }
    
    /**
     * Check if request should be blocked
     */
    private function isBlockedRequest(Request $request): bool
    {
        // Block requests with suspicious user agents
        $blockedUserAgents = [
            'curl', 'wget', 'python-requests', 'bot', 'crawler',
            'scanner', 'spider', 'scraper'
        ];
        
        $userAgent = strtolower($request->userAgent() ?? '');
        
        foreach ($blockedUserAgents as $blocked) {
            if (strpos($userAgent, $blocked) !== false) {
                // Allow legitimate bots but log them
                if (!$this->isLegitimateBot($userAgent)) {
                    return true;
                }
            }
        }
        
        // Block requests with no user agent
        if (empty($userAgent)) {
            return true;
        }
        
        // Block requests with suspicious referers
        $referer = $request->header('referer');
        if ($referer && $this->isSuspiciousReferer($referer)) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Check if the bot is legitimate
     */
    private function isLegitimateBot(string $userAgent): bool
    {
        $legitimateBots = [
            'googlebot', 'bingbot', 'slurp', 'duckduckbot',
            'baiduspider', 'yandexbot', 'facebookexternalhit',
            'twitterbot', 'linkedinbot', 'whatsapp'
        ];
        
        foreach ($legitimateBots as $bot) {
            if (strpos($userAgent, $bot) !== false) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Check if referer is suspicious
     */
    private function isSuspiciousReferer(string $referer): bool
    {
        $suspiciousDomains = [
            'suspicious-site.com',
            'malware-domain.net',
            // Add known malicious domains
        ];
        
        foreach ($suspiciousDomains as $domain) {
            if (strpos($referer, $domain) !== false) {
                return true;
            }
        }
        
        return false;
    }
}
