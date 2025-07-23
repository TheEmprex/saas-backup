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
            // Redirect to a nice UI page explaining job posting restrictions
            return redirect()->route('marketplace.job-posting.restricted')
                ->with('user_type', $user->userType?->display_name ?? 'User')
                ->with('can_apply', $user->isChatter() || $user->isVA());
        }

        return $next($request);
    }
}
