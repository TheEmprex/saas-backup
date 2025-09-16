<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeadersMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Prevent XSS attacks
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        
        // Prevent MIME type sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        
        // Prevent clickjacking
        $response->headers->set('X-Frame-Options', 'DENY');
        
        // Content Security Policy
        $csp = implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://js.stripe.com https://checkout.stripe.com",
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com",
            "font-src 'self' https://fonts.gstatic.com",
            "img-src 'self' data: https: blob:",
            "connect-src 'self' https://api.stripe.com https://checkout.stripe.com wss:",
            "frame-src https://js.stripe.com https://checkout.stripe.com",
            "object-src 'none'",
            "base-uri 'self'",
            "form-action 'self'",
            "frame-ancestors 'none'",
            "upgrade-insecure-requests"
        ]);
        
        if (app()->environment('production')) {
            $response->headers->set('Content-Security-Policy', $csp);
        } else {
            // More lenient CSP for development + allow Vite dev server (HMR)
            $devCsp = implode('; ', [
                "default-src 'self'",
                // Allow inline/eval for local dev + Vite client from localhost:5174
                "script-src 'self' 'unsafe-inline' 'unsafe-eval' http://localhost:5174 http://127.0.0.1:5174 https://js.stripe.com https://checkout.stripe.com",
                // Allow styles from Vite dev server and Google Fonts
                "style-src 'self' 'unsafe-inline' http://localhost:5174 http://127.0.0.1:5174 https://fonts.googleapis.com",
                // Fonts
                "font-src 'self' https://fonts.gstatic.com",
                // Images
                "img-src 'self' data: https: blob:",
                // Allow HMR websocket + Stripe endpoints + local Reverb (8080)
                "connect-src 'self' http://localhost:5174 http://127.0.0.1:5174 ws://localhost:5174 ws://127.0.0.1:5174 http://localhost:8080 http://127.0.0.1:8080 ws://localhost:8080 ws://127.0.0.1:8080 https://api.stripe.com https://checkout.stripe.com wss:",
                // Frames
                "frame-src https://js.stripe.com https://checkout.stripe.com",
                "object-src 'none'",
                "base-uri 'self'",
                "form-action 'self'",
                // In dev, allow being framed by self only to avoid breaking some tooling
                "frame-ancestors 'self'",
                "upgrade-insecure-requests"
            ]);
            $response->headers->set('Content-Security-Policy', $devCsp);
        }
        
        // HTTP Strict Transport Security (HTTPS only in production)
        if (app()->environment('production') && $request->secure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        }
        
        // Referrer Policy
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        
        // Feature Policy / Permissions Policy
        $response->headers->set('Permissions-Policy', implode(', ', [
            'camera=()',
            'microphone=()',
            'geolocation=()',
            'gyroscope=()',
            'magnetometer=()',
            'payment=(self)',
            'usb=()'
        ]));
        
        // Remove server information
        $response->headers->remove('Server');
        $response->headers->remove('X-Powered-By');
        
        // Cache control for sensitive pages
        if ($request->is('admin/*') || $request->is('api/*')) {
            $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', '0');
        }

        return $response;
    }
}
