<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\FeaturedJobPost;
use App\Models\JobPost;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class JobPaymentController extends Controller
{
    /**
     * Show payment form for job features.
     */
    public function show(Request $request)
    {
        $jobData = session('job_data');

        if (! $jobData) {
            return redirect()->route('marketplace.jobs.create')
                ->with('error', 'Job data not found. Please try again.');
        }

        $user = Auth::user();
        $isFeatured = isset($jobData['is_featured']) && $jobData['is_featured'];
        $isUrgent = isset($jobData['is_urgent']) && $jobData['is_urgent'];

        $featuredCost = $isFeatured && ! $user->canUseFeaturedForFree() ? 10 : 0;
        $urgentCost = $isUrgent ? 5 : 0;
        $totalCost = $featuredCost + $urgentCost;

        return view('marketplace.jobs.payment', ['jobData' => $jobData, 'totalCost' => $totalCost, 'featuredCost' => $featuredCost, 'urgentCost' => $urgentCost]);
    }

    /**
     * Process payment for job features.
     */
    public function process(Request $request)
    {
        $jobData = session('job_data');

        if (! $jobData) {
            return redirect()->route('marketplace.jobs.create')
                ->with('error', 'Job data not found. Please try again.');
        }

        $user = Auth::user();
        $isFeatured = isset($jobData['is_featured']) && $jobData['is_featured'];
        $isUrgent = isset($jobData['is_urgent']) && $jobData['is_urgent'];

        $featuredCost = $isFeatured && ! $user->canUseFeaturedForFree() ? 10 : 0;
        $urgentCost = $isUrgent ? 5 : 0;
        $totalCost = $featuredCost + $urgentCost;

        try {
            DB::transaction(function () use ($jobData, $featuredCost, $urgentCost, $totalCost): void {
                // Create the job post
                $jobPost = JobPost::create(array_merge($jobData, [
                    'user_id' => Auth::id(),
                    'featured_cost' => $featuredCost,
                    'urgent_cost' => $urgentCost,
                    'feature_payment_required' => $totalCost > 0,
                    'payment_status' => $totalCost > 0 ? 'pending' : 'completed',
                    'payment_completed_at' => $totalCost > 0 ? null : now(),
                ]));

                // If payment is required, create a payment intent
                if ($totalCost > 0) {
                    // Here you would integrate with Stripe or another payment processor
                    // For now, we'll simulate a successful payment
                    $jobPost->update([
                        'payment_status' => 'completed',
                        'payment_completed_at' => now(),
                        'payment_intent_id' => 'demo_payment_'.time(),
                    ]);
                }

                // Create featured job post record if applicable
                if ($featuredCost > 0) {
                    FeaturedJobPost::create([
                        'user_id' => Auth::id(),
                        'job_post_id' => $jobPost->id,
                        'amount' => $featuredCost,
                        'expires_at' => now()->addDays(30),
                    ]);
                }
            });

            // Clear session data
            session()->forget('job_data');

            return redirect()->route('marketplace.jobs.index')
                ->with('success', 'Job posted successfully!'.($totalCost > 0 ? ' Payment of $'.$totalCost.' processed.' : ''));

        } catch (Exception) {
            return redirect()->back()
                ->with('error', 'Failed to process payment. Please try again.');
        }
    }

    /**
     * Handle payment success callback.
     */
    public function success(Request $request)
    {
        $jobId = $request->get('job_id');
        $jobPost = JobPost::findOrFail($jobId);

        if ($jobPost->user_id !== Auth::id()) {
            abort(403);
        }

        $jobPost->update([
            'payment_status' => 'completed',
            'payment_completed_at' => now(),
        ]);

        return redirect()->route('marketplace.jobs.show', $jobPost)
            ->with('success', 'Payment completed successfully! Your job is now live.');
    }

    /**
     * Handle payment failure callback.
     */
    public function failure(Request $request)
    {
        $jobId = $request->get('job_id');
        $jobPost = JobPost::findOrFail($jobId);

        if ($jobPost->user_id !== Auth::id()) {
            abort(403);
        }

        $jobPost->update([
            'payment_status' => 'failed',
        ]);

        return redirect()->route('marketplace.jobs.create')
            ->with('error', 'Payment failed. Please try again.');
    }
}
