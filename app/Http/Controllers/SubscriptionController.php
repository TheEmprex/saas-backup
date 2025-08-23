<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SubscriptionPlan;
use App\Services\SubscriptionService;

class SubscriptionController extends Controller
{
    protected $subscriptionService;
    
    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
        $this->middleware('auth')->except(['plans']);
    }
    
    /**
     * Display available subscription plans.
     */
    public function plans()
    {
        $user = Auth::user();
        
        // If user is not authenticated, show all plans for agency type (default)
        if (!$user) {
            $userType = 'agency';
            $plans = $this->subscriptionService->getAvailablePlans($userType);
            $currentStats = ['plan_name' => null, 'has_subscription' => false];
            return view('subscription.plans', compact('plans', 'currentStats', 'user'));
        }
        
        // Default to 'agency' if userType is null
        $userType = $user->userType ? $user->userType->name : 'agency';
        $plans = $this->subscriptionService->getAvailablePlans($userType);
        $currentStats = $this->subscriptionService->getSubscriptionStats($user);
        
        return view('subscription.plans', compact('plans', 'currentStats', 'user'));
    }
    
    /**
     * Display the subscription dashboard.
     */
    public function dashboard()
    {
        $user = Auth::user();
        $stats = $this->subscriptionService->getSubscriptionStats($user);
        
        return view('subscription.dashboard', compact('stats', 'user'));
    }
    
    /**
     * Handle subscription upgrade/change.
     */
    public function subscribe(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:subscription_plans,id',
        ]);
        
        $user = Auth::user();
        $plan = SubscriptionPlan::findOrFail($request->plan_id);
        
        // Check if user can upgrade to this plan
        if (!$this->subscriptionService->canUpgradeToPlan($user, $plan)) {
            return redirect()->back()->with('error', 'You cannot change to this plan.');
        }
        
        // Here you would typically integrate with a payment processor
        // For now, we'll just assign the plan directly
        
        if ($plan->price > 0) {
            // Redirect to payment processor
            return redirect()->route('subscription.payment', ['plan' => $plan->id]);
        } else {
            // Free plan, assign immediately
            $this->subscriptionService->assignPlan($user, $plan);
            return redirect()->route('subscription.dashboard')->with('success', 'Successfully subscribed to ' . $plan->name);
        }
    }
    
    /**
     * Show plan change preview.
     */
    public function planPreview(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:subscription_plans,id',
        ]);
        
        $user = Auth::user();
        $plan = SubscriptionPlan::findOrFail($request->plan_id);
        
        $preview = $this->subscriptionService->getPlanChangePreview($user, $plan);
        
        return response()->json($preview);
    }
    
    /**
     * Handle subscription upgrade.
     */
    public function upgrade(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:subscription_plans,id',
        ]);
        
        $user = Auth::user();
        $plan = SubscriptionPlan::findOrFail($request->plan_id);
        
        // Check if user can upgrade to this plan
        if (!$this->subscriptionService->canUpgradeToPlan($user, $plan)) {
            return redirect()->back()->with('error', 'You cannot change to this plan.');
        }
        
        if ($plan->price > 0) {
            // For paid plans, redirect to payment WITHOUT assigning the plan
            return redirect()->route('subscription.payment', ['plan' => $plan->id])
                ->with('pending_upgrade', true);
        } else {
            // Free plan, assign immediately
            $this->subscriptionService->assignPlan($user, $plan);
            return redirect()->route('subscription.dashboard')
                ->with('success', 'Successfully changed to ' . $plan->name . ' plan.');
        }
    }
    
    /**
     * Handle subscription downgrade.
     */
    public function downgrade(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:subscription_plans,id',
        ]);
        
        $user = Auth::user();
        $plan = SubscriptionPlan::findOrFail($request->plan_id);
        
        // Check usage warnings before downgrade
        $warnings = $this->subscriptionService->getDowngradeWarnings($user, $plan);
        
        if ($plan->price > 0) {
            // For paid plans, redirect to payment WITHOUT assigning the plan
            return redirect()->route('subscription.payment', ['plan' => $plan->id])
                ->with('pending_downgrade', true)
                ->with('warnings', $warnings);
        } else {
            // Free plan, assign immediately
            $this->subscriptionService->assignPlan($user, $plan);
            return redirect()->route('subscription.dashboard')
                ->with('success', 'Successfully changed to ' . $plan->name . ' plan.')
                ->with('warnings', $warnings);
        }
    }
    
    /**
     * Handle payment processing with Stripe integration.
     */
    public function payment(SubscriptionPlan $plan)
    {
        $user = Auth::user();
        
        try {
            $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));
            
            // Create Stripe checkout session
            $session = $stripe->checkout->sessions->create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => $plan->name . ' Subscription',
                            'description' => $plan->description,
                        ],
                        'unit_amount' => $plan->price * 100, // Convert to cents
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('subscription.payment.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('subscription.payment.cancel'),
                'metadata' => [
                    'user_id' => $user->id,
                    'plan_id' => $plan->id,
                ],
            ]);
            
            return redirect($session->url);
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Unable to create payment session. Please try again.');
        }
    }
    
    /**
     * Handle cancelled payment.
     */
    public function paymentCancel()
    {
        return redirect()->route('subscription.plans')
            ->with('error', 'Payment cancelled. No changes were made to your subscription.');
    }
    
    /**
     * Handle successful payment callback from Stripe.
     */
    public function paymentSuccess(Request $request)
    {
        $request->validate([
            'session_id' => 'required|string', // Stripe checkout session ID
        ]);
        
        $user = Auth::user();
        
        try {
            $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));
            $session = $stripe->checkout->sessions->retrieve($request->session_id);
            
            if ($session->payment_status === 'paid') {
                $planId = $session->metadata->plan_id;
                $plan = SubscriptionPlan::findOrFail($planId);
                $expiresAt = now()->addMonth(); // Subscription expires after 1 month
                
                // Assign the paid plan with expiration date
                $this->subscriptionService->assignPlan($user, $plan, $expiresAt);
                
                return redirect()->route('subscription.dashboard')
                    ->with('success', 'Payment successful! Your ' . $plan->name . ' subscription is now active.');
            } else {
                return redirect()->route('subscription.plans')
                    ->with('error', 'Payment was not completed. Please try again.');
            }
            
        } catch (\Exception $e) {
            return redirect()->route('subscription.plans')
                ->with('error', 'Unable to verify payment. Please contact support if you were charged.');
        }
    }
    
    /**
     * Cancel current subscription.
     */
    public function cancel()
    {
        $user = Auth::user();
        $this->subscriptionService->endCurrentSubscription($user);
        
        // Assign free plan
        $this->subscriptionService->assignFreePlan($user);
        
        return redirect()->route('subscription.dashboard')->with('success', 'Subscription cancelled. You have been moved to the free plan.');
    }
}
