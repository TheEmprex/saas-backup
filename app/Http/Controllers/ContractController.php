<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\ContractReview;
use App\Models\JobPost;
use App\Models\Message;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ContractController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $userId = Auth::id();

        // Get contracts where user is either employer or contractor
        $contracts = Contract::with(['employer', 'contractor', 'jobPost'])
            ->where('employer_id', $userId)
            ->orWhere('contractor_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('contracts.index', ['contracts' => $contracts]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $userId = Auth::id();
        $users = User::query()->whereIn('id', function ($query) use ($userId): void {
            $query->select('sender_id')
                ->from('messages')
                ->where('recipient_id', $userId)
                ->union(
                    Message::select('recipient_id')
                        ->where('sender_id', $userId)
                );
        })
            ->where('id', '!=', $userId)
            ->select('id', 'name', 'email')
            ->orderBy('name')
            ->get();

        // Get job posts created by the user
        $jobPosts = JobPost::query()->where('user_id', $userId)
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('contracts.create', ['users' => $users, 'jobPosts' => $jobPosts]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'contractor_id' => 'required|exists:users,id',
                'contract_type' => 'required|in:hourly,fixed,commission',
                'rate' => 'required|numeric|min:0',
                'description' => 'required|string|max:2000',
                'start_date' => 'required|date|after_or_equal:today',
                'job_post_id' => 'nullable|exists:job_posts,id',
            ]);

            $jobPost = null;

            if (isset($validated['job_post_id'])) {
                $jobPost = JobPost::find($validated['job_post_id']);
            }

            // Ensure user can't create contract with themselves
            if ($validated['contractor_id'] == Auth::id()) {
                return response()->json(['success' => false, 'error' => 'You cannot create a contract with yourself.'], 400);
            }

            // Prepare contract data
            $contractData = [
                'employer_id' => Auth::id(),
                'contractor_id' => $validated['contractor_id'],
                'contract_type' => $validated['contract_type'],
                'description' => $validated['description'],
                'start_date' => $validated['start_date'],
                'status' => 'active',
            ];

            if ($jobPost) {
                $contractData['job_post_id'] = $jobPost->id;

                // Pre-populate description from job post if not provided
                if (empty($validated['description'])) {
                    $contractData['description'] = $jobPost->description;
                }

                // Pre-populate rate from job post if not provided
                if (empty($validated['rate']) && $jobPost->rate_type === $validated['contract_type']) {
                    if ($validated['contract_type'] === 'hourly') {
                        $contractData['rate'] = $jobPost->hourly_rate;
                    } elseif ($validated['contract_type'] === 'fixed') {
                        $contractData['rate'] = $jobPost->fixed_rate;
                    } elseif ($validated['contract_type'] === 'commission') {
                        $contractData['commission_percentage'] = $jobPost->commission_percentage;
                    }
                }
            }

            // Set rate based on contract type (if not already set by job post)
            if (! isset($contractData['rate']) && ! isset($contractData['commission_percentage'])) {
                if ($validated['contract_type'] === 'commission') {
                    $contractData['commission_percentage'] = $validated['rate'];
                } else {
                    $contractData['rate'] = $validated['rate'];
                }
            }

            $contract = Contract::create($contractData);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'contract' => $contract->load(['employer', 'contractor']),
                ]);
            }

            return redirect()->route('contracts.index')
                ->with('success', 'Contract created successfully!');

        } catch (Exception $exception) {
            Log::error('Contract creation error: '.$exception->getMessage());

            if ($request->ajax()) {
                return response()->json(['success' => false, 'error' => 'Failed to create contract.'], 500);
            }

            return redirect()->back()->with('error', 'Failed to create contract.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Contract $contract)
    {
        // Check if user is part of this contract
        if ($contract->employer_id !== Auth::id() && $contract->contractor_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this contract.');
        }

        $contract->load(['employer', 'contractor', 'reviews.reviewer', 'reviews.reviewedUser', 'jobPost']);

        return view('contracts.show', ['contract' => $contract]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Contract $contract)
    {
        if (! $contract->canBeEditedBy(Auth::user())) {
            abort(403, 'Unauthorized to edit this contract.');
        }

        $contract->load(['employer', 'contractor']);

        return view('contracts.edit', ['contract' => $contract]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Contract $contract)
    {
        // Check if user is part of this contract
        if ($contract->employer_id !== Auth::id() && $contract->contractor_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this contract.');
        }

        $validated = $request->validate([
            'status' => 'sometimes|in:active,completed,cancelled,suspended',
            'end_date' => 'sometimes|date|after:start_date',
        ]);

        $contract->update($validated);

        return redirect()->route('contracts.show', $contract)
            ->with('success', 'Contract updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contract $contract)
    {
        if (! $contract->canBeDeletedBy(Auth::user())) {
            abort(403, 'Unauthorized to delete this contract.');
        }

        try {
            $contract->delete();

            return redirect()->route('contracts.index')
                ->with('success', 'Contract deleted successfully!');
        } catch (Exception $exception) {
            Log::error('Contract deletion error: '.$exception->getMessage());

            return redirect()->back()
                ->with('error', 'Failed to delete contract.');
        }
    }

    /**
     * Add earnings to a contract
     */
    public function addEarning(Request $request, Contract $contract)
    {
        // Only employer can add earnings
        if ($contract->employer_id !== Auth::id()) {
            abort(403, 'Only the employer can add earnings.');
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string|max:255',
            'hours' => 'nullable|integer|min:0',
        ]);

        $contract->addEarning(
            $validated['amount'],
            $validated['description'],
            $validated['hours'] ?? null
        );

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'contract' => $contract->fresh(),
            ]);
        }

        return redirect()->route('contracts.show', $contract)
            ->with('success', 'Earnings added successfully!');
    }

    /**
     * Remove earnings from a contract
     */
    public function removeEarning(Request $request, Contract $contract, int $earningIndex)
    {
        // Only employer can remove earnings
        if ($contract->employer_id !== Auth::id()) {
            abort(403, 'Only the employer can remove earnings.');
        }

        $success = $contract->removeEarning($earningIndex);

        if ($request->ajax()) {
            return response()->json([
                'success' => $success,
                'contract' => $success ? $contract->fresh() : null,
            ]);
        }

        if ($success) {
            return redirect()->route('contracts.show', $contract)
                ->with('success', 'Earnings removed successfully!');
        }

        return redirect()->route('contracts.show', $contract)
            ->with('error', 'Failed to remove earnings.');
    }

    /**
     * Store a review for a contract
     */
    public function storeReview(Request $request, Contract $contract)
    {
        if (! $contract->canBeReviewedBy(Auth::user())) {
            abort(403, 'You cannot review this contract.');
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'skills_ratings' => 'nullable|array',
            'skills_ratings.*' => 'integer|min:1|max:5',
            'would_work_again' => 'boolean',
            'recommend_to_others' => 'boolean',
        ]);

        $otherParty = $contract->getOtherParty(Auth::user());

        $review = ContractReview::create([
            'contract_id' => $contract->id,
            'reviewer_id' => Auth::id(),
            'reviewed_user_id' => $otherParty->id,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? null,
            'skills_ratings' => $validated['skills_ratings'] ?? null,
            'would_work_again' => $validated['would_work_again'] ?? false,
            'recommend_to_others' => $validated['recommend_to_others'] ?? false,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'review' => $review->load(['reviewer', 'reviewedUser']),
            ]);
        }

        return redirect()->route('contracts.show', $contract)
            ->with('success', 'Review submitted successfully!');
    }

    /**
     * Show the form for editing a review
     */
    public function editReview(Contract $contract, ContractReview $review)
    {
        if ($review->reviewer_id !== Auth::id()) {
            abort(403, 'You can only edit your own reviews.');
        }

        $contract->load(['employer', 'contractor']);
        $review->load(['reviewer', 'reviewedUser']);

        return view('contracts.reviews.edit', ['contract' => $contract, 'review' => $review]);
    }

    /**
     * Update a review
     */
    public function updateReview(Request $request, Contract $contract, ContractReview $review)
    {
        if ($review->reviewer_id !== Auth::id()) {
            abort(403, 'You can only edit your own reviews.');
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'skills_ratings' => 'nullable|array',
            'skills_ratings.*' => 'integer|min:1|max:5',
            'would_work_again' => 'boolean',
            'recommend_to_others' => 'boolean',
        ]);

        $review->update([
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? null,
            'skills_ratings' => $validated['skills_ratings'] ?? null,
            'would_work_again' => $validated['would_work_again'] ?? false,
            'recommend_to_others' => $validated['recommend_to_others'] ?? false,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'review' => $review->fresh()->load(['reviewer', 'reviewedUser']),
            ]);
        }

        return redirect()->route('contracts.show', $contract)
            ->with('success', 'Review updated successfully!');
    }

    /**
     * Remove a review
     */
    public function destroyReview(Contract $contract, ContractReview $review)
    {
        if ($review->reviewer_id !== Auth::id()) {
            abort(403, 'You can only delete your own reviews.');
        }

        try {
            $review->delete();

            return redirect()->route('contracts.show', $contract)
                ->with('success', 'Review deleted successfully!');
        } catch (Exception $exception) {
            Log::error('Review deletion error: '.$exception->getMessage());

            return redirect()->back()
                ->with('error', 'Failed to delete review.');
        }
    }

    /**
     * Get job post details for pre-population
     */
    public function getJobPostDetails(Request $request, JobPost $jobPost)
    {
        // Check if user owns this job post
        if ($jobPost->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this job post.');
        }

        return response()->json([
            'id' => $jobPost->id,
            'title' => $jobPost->title,
            'description' => $jobPost->description,
            'rate_type' => $jobPost->rate_type,
            'hourly_rate' => $jobPost->hourly_rate,
            'fixed_rate' => $jobPost->fixed_rate,
            'commission_percentage' => $jobPost->commission_percentage,
        ]);
    }

    /**
     * Terminate contract and leave review
     */
    public function terminateAndReview(Request $request, Contract $contract)
    {
        // Check if user is part of this contract
        if ($contract->employer_id !== Auth::id() && $contract->contractor_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this contract.');
        }

        // Check if contract is active
        if ($contract->status !== 'active') {
            return redirect()->back()->with('error', 'Only active contracts can be terminated.');
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'would_work_again' => 'boolean',
            'recommend_to_others' => 'boolean',
        ]);

        try {
            DB::transaction(function () use ($contract, $validated): void {
                // Update contract status to completed
                $contract->update([
                    'status' => 'completed',
                    'end_date' => now(),
                ]);

                // Create review
                $otherParty = $contract->getOtherParty(Auth::user());

                ContractReview::create([
                    'contract_id' => $contract->id,
                    'reviewer_id' => Auth::id(),
                    'reviewed_user_id' => $otherParty->id,
                    'rating' => $validated['rating'],
                    'comment' => $validated['comment'] ?? null,
                    'would_work_again' => $validated['would_work_again'] ?? false,
                    'recommend_to_others' => $validated['recommend_to_others'] ?? false,
                ]);
            });

            return redirect()->route('contracts.show', $contract)
                ->with('success', 'Contract terminated and review submitted successfully!');
        } catch (Exception $exception) {
            Log::error('Contract termination error: '.$exception->getMessage());

            return redirect()->back()->with('error', 'Failed to terminate contract and submit review.');
        }
    }
}
