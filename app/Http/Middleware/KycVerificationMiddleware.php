<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class KycVerificationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        
        if (!$user) {
            return redirect()->route('login');
        }
        
        // Check verification based on user type
        if ($user->userType && $user->userType->name === 'chatter') {
            // Chatters require KYC verification
            if (!$user->hasKycSubmitted()) {
                return redirect()->route('profile.kyc')
                    ->with('error', 'You must complete KYC verification to access this feature.');
            }
            
            // Check if KYC is approved
            if (!$user->isKycVerified()) {
                return redirect()->route('profile.kyc')
                    ->with('warning', 'Your KYC verification is pending approval. You cannot access this feature yet.');
            }
        }
        
        return $next($request);
    }
}
