<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserCanPostJobs
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Check if user is authorized to post jobs (only agencies)
        if (!$user->isAgency() && !$user->isAdmin()) {
            // Provide helpful feedback based on user type
            if ($user->isChatter() || $user->isVA()) {
                return redirect()->route('marketplace.jobs')->with('error', 'Only agencies can post jobs. As a ' . $user->userType->display_name . ', you can browse and apply to jobs instead.');
            } else {
                return redirect()->route('marketplace.jobs')->with('error', 'Only agencies are authorized to post jobs.');
            }
        }

        return $next($request);
    }
}
