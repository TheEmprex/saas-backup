<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\JobPost;
use App\Models\Rating;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RatingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();

        // Use contract reviews instead of old ratings
        $givenRatings = $user->contractReviewsGiven()
            ->with(['reviewedUser', 'contract'])
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'given');

        $receivedRatings = $user->contractReviewsReceived()
            ->with(['reviewer', 'contract'])
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'received');

        // Use contract review statistics
        $averageRating = $user->average_contract_rating;
        $totalRatings = $user->total_contract_reviews;
        $ratingBreakdown = null; // Not implemented for contract reviews yet

        return view('theme::ratings.index', ['givenRatings' => $givenRatings, 'receivedRatings' => $receivedRatings, 'averageRating' => $averageRating, 'ratingBreakdown' => $ratingBreakdown, 'totalRatings' => $totalRatings]);
    }

    public function create(Request $request)
    {
        $ratedUser = User::findOrFail($request->rated_id);
        $jobPost = $request->job_post_id ? JobPost::findOrFail($request->job_post_id) : null;

        // Check if user can rate this person
        if (! $this->canRate(Auth::user(), $ratedUser, $jobPost)) {
            return redirect()->back()->with('error', 'You are not authorized to rate this user.');
        }

        return view('theme::ratings.create', ['ratedUser' => $ratedUser, 'jobPost' => $jobPost]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'rated_id' => 'required|exists:users,id',
            'job_post_id' => 'nullable|exists:job_posts,id',
            'overall_rating' => 'required|integer|min:1|max:5',
            'communication_rating' => 'nullable|integer|min:1|max:5',
            'professionalism_rating' => 'nullable|integer|min:1|max:5',
            'timeliness_rating' => 'nullable|integer|min:1|max:5',
            'quality_rating' => 'nullable|integer|min:1|max:5',
            'review_title' => 'nullable|string|max:255',
            'review_content' => 'nullable|string|max:1000',
            'is_public' => 'boolean',
        ]);

        $ratedUser = User::findOrFail($request->rated_id);
        $jobPost = $request->job_post_id ? JobPost::findOrFail($request->job_post_id) : null;

        // Check if user can rate this person
        if (! $this->canRate(Auth::user(), $ratedUser, $jobPost)) {
            return redirect()->back()->with('error', 'You are not authorized to rate this user.');
        }

        // Check if rating already exists
        $existingRating = Rating::query()->where([
            'rater_id' => Auth::id(),
            'rated_id' => $request->rated_id,
            'job_post_id' => $request->job_post_id,
        ])->first();

        if ($existingRating) {
            return redirect()->back()->with('error', 'You have already rated this user for this job.');
        }

        Rating::create([
            'rater_id' => Auth::id(),
            'rated_id' => $request->rated_id,
            'job_post_id' => $request->job_post_id,
            'overall_rating' => $request->overall_rating,
            'communication_rating' => $request->communication_rating,
            'professionalism_rating' => $request->professionalism_rating,
            'timeliness_rating' => $request->timeliness_rating,
            'quality_rating' => $request->quality_rating,
            'review_title' => $request->review_title,
            'review_content' => $request->review_content,
            'is_public' => $request->boolean('is_public', true),
        ]);

        return redirect()->route('ratings.index')
            ->with('success', 'Rating submitted successfully.');
    }

    public function show(Rating $rating)
    {
        // Only allow viewing public ratings or ratings involving the current user
        if (! $rating->is_public && $rating->rater_id !== Auth::id() && $rating->rated_id !== Auth::id()) {
            abort(403);
        }

        $rating->load(['rater', 'rated', 'jobPost']);

        return view('theme::ratings.show', ['rating' => $rating]);
    }

    public function edit(Rating $rating)
    {
        // Only allow editing own ratings
        if ($rating->rater_id !== Auth::id()) {
            abort(403);
        }

        $rating->load(['rated', 'jobPost']);

        return view('theme::ratings.edit', ['rating' => $rating]);
    }

    public function update(Request $request, Rating $rating)
    {
        // Only allow updating own ratings
        if ($rating->rater_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'overall_rating' => 'required|integer|min:1|max:5',
            'communication_rating' => 'nullable|integer|min:1|max:5',
            'professionalism_rating' => 'nullable|integer|min:1|max:5',
            'timeliness_rating' => 'nullable|integer|min:1|max:5',
            'quality_rating' => 'nullable|integer|min:1|max:5',
            'review_title' => 'nullable|string|max:255',
            'review_content' => 'nullable|string|max:1000',
            'is_public' => 'boolean',
        ]);

        $rating->update([
            'overall_rating' => $request->overall_rating,
            'communication_rating' => $request->communication_rating,
            'professionalism_rating' => $request->professionalism_rating,
            'timeliness_rating' => $request->timeliness_rating,
            'quality_rating' => $request->quality_rating,
            'review_title' => $request->review_title,
            'review_content' => $request->review_content,
            'is_public' => $request->boolean('is_public', true),
        ]);

        return redirect()->route('ratings.index')
            ->with('success', 'Rating updated successfully.');
    }

    public function destroy(Rating $rating)
    {
        // Only allow deleting own ratings
        if ($rating->rater_id !== Auth::id()) {
            abort(403);
        }

        $rating->delete();

        return redirect()->route('ratings.index')
            ->with('success', 'Rating deleted successfully.');
    }

    private function canRate(User $rater, User $rated, ?JobPost $jobPost = null): bool
    {
        // Can't rate yourself
        if ($rater->id === $rated->id) {
            return false;
        }

        // If job post is specified, check if users were involved
        if ($jobPost instanceof \App\Models\JobPost) {
            // Check if rater was the job poster or an accepted applicant
            $isJobPoster = $jobPost->user_id === $rater->id;
            $isAcceptedApplicant = $jobPost->applications()
                ->where('user_id', $rater->id)
                ->where('status', 'accepted')
                ->exists();

            // Check if rated was the job poster or an accepted applicant
            $ratedIsJobPoster = $jobPost->user_id === $rated->id;
            $ratedIsAcceptedApplicant = $jobPost->applications()
                ->where('user_id', $rated->id)
                ->where('status', 'accepted')
                ->exists();

            // Both users must have been involved in the job
            return ($isJobPoster || $isAcceptedApplicant) && ($ratedIsJobPoster || $ratedIsAcceptedApplicant);
        }

        // For general ratings, check if users have had some interaction
        // This could be through messaging, job applications, etc.
        return true; // For now, allow any registered user to rate others
    }
}
