<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\IdentityBlacklist;
use App\Models\User;
use App\Models\KycVerification;
use Illuminate\Support\Facades\DB;

class PreventDuplicateRegistration
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only apply to registration POST requests
        if ($request->isMethod('post') && $request->routeIs('custom.register.post')) {
            $errors = [];
            
            // Get client IP
            $clientIp = $request->ip();
            
            // Check if IP is blacklisted
            if (IdentityBlacklist::isIpBlacklisted($clientIp)) {
                $errors[] = 'Registration is not allowed from this location.';
            }
            
            // Check if email is blacklisted
            if ($request->email && IdentityBlacklist::isEmailBlacklisted($request->email)) {
                $errors[] = 'This email address is not allowed to register.';
            }
            
            // Check for phone number if provided
            if ($request->phone_number && IdentityBlacklist::isPhoneBlacklisted($request->phone_number)) {
                $errors[] = 'This phone number is not allowed to register.';
            }
            
            // Check if phone number already exists in users table
            if ($request->phone_number && User::where('phone_number', $request->phone_number)->exists()) {
                $errors[] = 'This phone number is already registered.';
            }
            
            if (!empty($errors)) {
                return back()->withErrors([
                    'registration_blocked' => 'Registration blocked: ' . implode(' ', $errors)
                ])->withInput($request->except('password', 'password_confirmation'));
            }
        }
        
        return $next($request);
    }
}
