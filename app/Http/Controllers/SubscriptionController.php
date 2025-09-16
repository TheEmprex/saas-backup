<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    public function __construct(protected \App\Services\SubscriptionService $subscriptionService)
    {
        $this->middleware('auth')->except(['plans']);
    }

    /**
     * Display available subscription plans.
     */
    public function plans()
    {
        $user = Auth::user();

        // If user is not authenticated, show all plans for agency type (default)
        if (! $user) {
            $userType = 'agency';
            $plans = $this->subscriptionService->getAvailablePlans($userType);
            $currentStats = ['plan_name' => null, 'has_subscription' => false];

            return view('subscription.plans', ['plans' => $plans, 'currentStats' => $currentStats, 'user' => $user]);
        }

        $userType = $user->userType->name;
        $plans = $this->subscriptionService->getAvailablePlans($userType);
        $currentStats = $this->subscriptionService->getSubscriptionStats($user);

        return view('subscription.plans', ['plans' => $plans, 'currentStats' => $currentStats, 'user' => $user]);
    }

    /**
     * Display the subscription dashboard.
     */
    public function dashboard()
    {
        $user = Auth::user();
        $stats = $this->subscriptionService->getSubscriptionStats($user);

        return view('subscription.dashboard', ['stats' => $stats, 'user' => $user]);
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
        if (! $this->subscriptionService->canUpgradeToPlan($user, $plan)) {
            return redirect()->back()->with('error', 'You cannot change to this plan.');
        }

        // Here you would typically integrate with a payment processor
        // For now, we'll just assign the plan directly

        if ($plan->price > 0) {
            // Redirect to payment processor
            return redirect()->route('subscription.payment', ['plan' => $plan->id]);
        }

        // Free plan, assign immediately
        $this->subscriptionService->assignPlan($user, $plan);

        return redirect()->route('subscription.dashboard')->with('success', 'Successfully subscribed to '.$plan->name);
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
        if (! $this->subscriptionService->canUpgradeToPlan($user, $plan)) {
            return redirect()->back()->with('error', 'You cannot change to this plan.');
        }

        if ($plan->price > 0) {
            // For paid plans, redirect to payment WITHOUT assigning the plan
            return redirect()->route('subscription.payment', ['plan' => $plan->id])
                ->with('pending_upgrade', true);
        }

        // Free plan, assign immediately
        $this->subscriptionService->assignPlan($user, $plan);

        return redirect()->route('subscription.dashboard')
            ->with('success', 'Successfully changed to '.$plan->name.' plan.');
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
        }

        // Free plan, assign immediately
        $this->subscriptionService->assignPlan($user, $plan);

        return redirect()->route('subscription.dashboard')
            ->with('success', 'Successfully changed to '.$plan->name.' plan.')
            ->with('warnings', $warnings);
    }

    /**
     * Handle payment processing (placeholder).
     */
    public function payment(SubscriptionPlan $plan)
    {
        $user = Auth::user();

        // This would integrate with Stripe, PayPal, etc.
        return view('subscription.payment', ['plan' => $plan, 'user' => $user]);
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
     * Handle successful payment callback.
     */
    public function paymentSuccess(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:subscription_plans,id',
            'payment_method' => 'required|string',
            'transaction_id' => 'required|string',
        ]);

        $user = Auth::user();
        $plan = SubscriptionPlan::findOrFail($request->plan_id);

        // Verify payment with payment processor here
        // For demo purposes, we'll assume payment is verified

        // Only assign the subscription AFTER successful payment verification
        $expiresAt = now()->addMonth();
        $this->subscriptionService->assignPlan($user, $plan, $expiresAt);

        return redirect()->route('subscription.dashboard')
            ->with('success', 'Payment successful! Your '.$plan->name.' subscription is now active.');
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
