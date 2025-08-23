<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\SubscriptionPlan;
use App\Services\SubscriptionService;

class StripeWebhookController extends Controller
{
    protected $subscriptionService;

    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }

    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');
        $endpoint_secret = config('services.stripe.webhook_secret');

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload, $sig_header, $endpoint_secret
            );
        } catch (\UnexpectedValueException $e) {
            Log::error('Invalid Stripe webhook payload', ['error' => $e->getMessage()]);
            return response('Invalid payload', 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            Log::error('Invalid Stripe webhook signature', ['error' => $e->getMessage()]);
            return response('Invalid signature', 400);
        }

        // Handle the event
        switch ($event['type']) {
            case 'checkout.session.completed':
                $this->handleCheckoutSessionCompleted($event['data']['object']);
                break;
            case 'invoice.payment_succeeded':
                $this->handleInvoicePaymentSucceeded($event['data']['object']);
                break;
            case 'invoice.payment_failed':
                $this->handleInvoicePaymentFailed($event['data']['object']);
                break;
            case 'customer.subscription.deleted':
                $this->handleSubscriptionDeleted($event['data']['object']);
                break;
            default:
                Log::info('Unhandled Stripe webhook event', ['type' => $event['type']]);
        }

        return response('Webhook handled', 200);
    }

    private function handleCheckoutSessionCompleted($session)
    {
        Log::info('Handling checkout session completed', ['session_id' => $session['id']]);

        try {
            $userId = $session['metadata']['user_id'] ?? null;
            $planId = $session['metadata']['plan_id'] ?? null;

            if (!$userId || !$planId) {
                Log::error('Missing metadata in checkout session', [
                    'session_id' => $session['id'],
                    'user_id' => $userId,
                    'plan_id' => $planId
                ]);
                return;
            }

            $user = User::find($userId);
            $plan = SubscriptionPlan::find($planId);

            if (!$user || !$plan) {
                Log::error('User or plan not found', [
                    'user_id' => $userId,
                    'plan_id' => $planId
                ]);
                return;
            }

            // Assign the plan with 1-month expiry
            $expiresAt = now()->addMonth();
            $this->subscriptionService->assignPlan($user, $plan, $expiresAt);

            Log::info('Successfully assigned subscription plan via webhook', [
                'user_id' => $userId,
                'plan_id' => $planId,
                'expires_at' => $expiresAt
            ]);

        } catch (\Exception $e) {
            Log::error('Error handling checkout session completed', [
                'error' => $e->getMessage(),
                'session_id' => $session['id']
            ]);
        }
    }

    private function handleInvoicePaymentSucceeded($invoice)
    {
        Log::info('Handling invoice payment succeeded', ['invoice_id' => $invoice['id']]);
        // Handle recurring payments here if implementing subscription renewals
    }

    private function handleInvoicePaymentFailed($invoice)
    {
        Log::info('Handling invoice payment failed', ['invoice_id' => $invoice['id']]);
        // Handle failed payments here (e.g., notify user, suspend account)
    }

    private function handleSubscriptionDeleted($subscription)
    {
        Log::info('Handling subscription deleted', ['subscription_id' => $subscription['id']]);
        // Handle subscription cancellation via Stripe
    }
}
