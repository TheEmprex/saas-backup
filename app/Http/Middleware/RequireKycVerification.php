<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RequireKycVerification
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Check verification based on user type's KYC requirement
        if ($user->userType && $user->userType->requires_kyc) {
            // Users with KYC-required types need verification
            if (!$user->hasKycSubmitted()) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'error' => 'KYC verification required',
                        'message' => 'You must complete KYC verification to access this feature.',
                        'redirect' => route('profile.kyc')
                    ], 403);
                }
                
                return redirect()->route('profile.kyc')
                    ->with('error', 'You must complete KYC verification to access this feature.');
            }
            
            // Check if KYC is approved
            if (!$user->isKycVerified()) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'error' => 'KYC verification pending',
                        'message' => 'Your KYC verification is pending approval. You cannot access this feature yet.',
                        'redirect' => route('profile.kyc')
                    ], 403);
                }
                
                return redirect()->route('profile.kyc')
                    ->with('warning', 'Your KYC verification is pending approval. You cannot access this feature yet.');
            }
        }

        return $next($request);
    }
}
