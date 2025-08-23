<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Services\SubscriptionService;

class CheckSubscriptionLimits
{
    protected $subscriptionService;
    
    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }
    
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $action = null, string $feature = null): Response
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }
        
        // Check specific subscription limits based on action
        switch ($action) {
            case 'job_post':
                if (!$user->canPostJob()) {
                    $message = 'You have reached your job posting limit for this month.';
                    if (!$user->hasActiveSubscription()) {
                        $message .= ' Please subscribe to a plan to post jobs.';
                    } else {
                        $message .= ' Please upgrade your subscription for more job posts.';
                    }
                    return $this->redirectWithSubscriptionError($message);
                }
                break;
                
            case 'job_application':
                if (!$user->canApplyToJob()) {
                    $remaining = $user->getRemainingJobApplications();
                    $message = 'You have reached your job application limit for this month.';
                    if (!$user->hasActiveSubscription()) {
                        $message .= ' Please subscribe to a plan for unlimited applications.';
                    } else {
                        $message .= ' Please upgrade your subscription for more applications.';
                    }
                    return $this->redirectWithSubscriptionError($message);
                }
                break;
                
            case 'premium_access':
                $jobPostId = $request->route('job_post') ?? $request->input('job_post_id');
                if ($jobPostId && !$user->hasAccessToPremiumJob($jobPostId)) {
                    return $this->redirectWithSubscriptionError(
                        'You need a premium subscription or microtransaction to access this job post.'
                    );
                }
                break;
                
            case 'subscription_required':
                if (!$user->hasActiveSubscription()) {
                    return redirect()->route('subscription.plans')->with('error', 'A subscription is required to access this feature.');
                }
                break;
                
            case 'feature_access':
                if ($feature && !$user->hasFeatureAccess($feature)) {
                    $tierInfo = $user->getSubscriptionTier();
                    $message = "This feature requires a premium subscription. You are currently on the {$tierInfo['name']} plan.";
                    return $this->redirectWithSubscriptionError($message);
                }
                break;
                
            case 'conversation_limit':
                if ($user->hasReachedLimit('conversations')) {
                    $message = 'You have reached your conversation limit.';
                    if (!$user->hasActiveSubscription()) {
                        $message .= ' Subscribe to create more conversations.';
                    } else {
                        $message .= ' Upgrade to unlimited conversations.';
                    }
                    return $this->redirectWithSubscriptionError($message);
                }
                break;
                
            case 'premium_content':
                if (!$user->canAccessPremiumContent()) {
                    return $this->redirectWithSubscriptionError(
                        'You need a paid subscription to access premium content.'
                    );
                }
                break;
        }
        
        return $next($request);
    }
    
    /**
     * Redirect with subscription error and tier information.
     */
    private function redirectWithSubscriptionError(string $message)
    {
        $user = Auth::user();
        $tierInfo = $user->getSubscriptionTier();
        $usage = $user->getSubscriptionUsage();
        
        return redirect()->back()->with([
            'error' => $message,
            'subscription_tier' => $tierInfo,
            'usage_stats' => $usage,
            'show_upgrade' => true
        ]);
    }
}
