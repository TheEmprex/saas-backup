<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnforceKycVerification
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            return redirect()->route('custom.login');
        }

        $user = auth()->user();
        
        // ADMINS have full access - skip all verification checks
        if ($user->isAdmin()) {
            return $next($request);
        }
        
        // For chatters, KYC is MANDATORY - no access to anything without it
        if ($user->isChatter() && !$user->isKycVerified()) {
            // Exception: allow access to KYC-related routes and essential routes
            $allowedRoutes = [
                // KYC routes
                'profile.kyc',
                'profile.kyc.submit', 
                'kyc.create',
                'kyc.store',
                'kyc.show',
                'kyc.index',
                'kyc.download',
                // Email verification routes
                'verification.notice',
                'verification.verify', 
                'verification.send',
                // Auth routes
                'custom.login',
                'logout',
                // Essential routes for redirections
                'dashboard', // Allow dashboard access so we can redirect properly
                // Admin routes (just in case)
                'admin.dashboard',
                'admin.users.index',
                'admin.kyc.index',
                'admin.earnings.index',
                'admin.jobs.index',
                'admin.messages.index',
                'platform.analytics',
            ];
            
            if (!in_array($request->route()->getName(), $allowedRoutes)) {
                return redirect()->route('kyc.index')
                    ->with('error', 'Chatters must complete KYC verification to access the platform.');
            }
        }
        
        // For agencies, earnings verification is also mandatory
        if ($user->isAgency() && !$user->isEarningsVerified()) {
            $allowedRoutes = [
                'profile.earnings-verification',
                'verification.notice',
                'verification.verify',
                'verification.send',
                'custom.login',
                'logout'
            ];
            
            if (!in_array($request->route()->getName(), $allowedRoutes)) {
                return redirect()->route('profile.earnings-verification')
                    ->with('error', 'Agencies must complete earnings verification to access the platform.');
            }
        }
        
        return $next($request);
    }
}
