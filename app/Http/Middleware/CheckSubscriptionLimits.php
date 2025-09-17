<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscriptionLimits
{
    public function __construct(protected \App\Services\SubscriptionService $subscriptionService)
    {
    }

    /**
     * Handle an incoming request.
     *
     * @param  Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ?string $action = null): Response
    {
        $user = Auth::user();

        if (! $user) {
            return redirect()->route('login');
        }

        // Check specific subscription limits based on action
        switch ($action) {
            case 'job_post':
                if (! $user->canPostJob()) {
                    return redirect()->back()->with('error', 'You have reached your job posting limit for this month. Please upgrade your subscription.');
                }

                break;

            case 'job_application':
                if (! $user->canApplyToJob()) {
                    return redirect()->back()->with('error', 'You have reached your job application limit for this month. Please upgrade your subscription.');
                }

                break;

            case 'premium_access':
                $jobPostId = $request->route('job_post') ?? $request->input('job_post_id');

                if ($jobPostId && ! $user->hasAccessToPremiumJob($jobPostId)) {
                    return redirect()->back()->with('error', 'You need a premium subscription or microtransaction to access this job post.');
                }

                break;

            case 'subscription_required':
                if (! $user->hasActiveSubscription()) {
                    return redirect()->route('subscription.plans')->with('error', 'A subscription is required to access this feature.');
                }

                break;
        }

        return $next($request);
    }
}
