<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ReviewContest;
use App\Models\Rating;

class ReviewContestController extends Controller
{
    /**
     * Display user's contest submissions.
     */
    public function index()
    {
        $contests = ReviewContest::with(['rating', 'reviewedBy'])
            ->where('contested_by', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('theme::ratings.contests.index', compact('contests'));
    }

    /**
     * Show the form for creating a new contest.
     */
    public function create(Rating $rating)
    {
        // Check if user can contest this rating
        if ($rating->rated_id !== Auth::id()) {
            abort(403, 'You can only contest ratings given to you.');
        }

        // Check if already contested
        $existingContest = ReviewContest::where('rating_id', $rating->id)->first();
        if ($existingContest) {
            return redirect()->route('ratings.contests.show', $existingContest)
                ->with('info', 'This rating is already being contested.');
        }

        return view('reviews.contests.create', compact('rating'));
    }

    /**
     * Store a newly created contest.
     */
    public function store(Request $request, Rating $rating)
    {
        // Check if user can contest this rating
        if ($rating->rated_id !== Auth::id()) {
            abort(403, 'You can only contest ratings given to you.');
        }

        // Check if already contested
        $existingContest = ReviewContest::where('rating_id', $rating->id)->first();
        if ($existingContest) {
            return redirect()->route('ratings.contests.show', $existingContest)
                ->with('info', 'This rating is already being contested.');
        }

        $request->validate([
            'reason' => 'required|string|min:20|max:1000',
            'evidence' => 'nullable|string|max:2000'
        ]);

        $contest = ReviewContest::create([
            'rating_id' => $rating->id,
            'contested_by' => Auth::id(),
            'reason' => $request->reason,
            'evidence' => $request->evidence,
            'status' => 'pending'
        ]);

        return redirect()->route('ratings.contests.show', $contest)
            ->with('success', 'Your contest has been submitted and is under review.');
    }

    /**
     * Display the specified contest.
     */
    public function show(ReviewContest $contest)
    {
        // Check if user can view this contest
        if ($contest->contested_by !== Auth::id()) {
            abort(403, 'You can only view your own contests.');
        }

        $contest->load(['rating.rater', 'rating.rated', 'reviewedBy']);

        return view('reviews.contests.show', compact('contest'));
    }

    /**
     * Cancel a pending contest.
     */
    public function cancel(ReviewContest $contest)
    {
        // Check if user can cancel this contest
        if ($contest->contested_by !== Auth::id()) {
            abort(403, 'You can only cancel your own contests.');
        }

        if (!$contest->isPending()) {
            return redirect()->back()
                ->with('error', 'Only pending contests can be cancelled.');
        }

        $contest->update([
            'status' => 'cancelled',
            'admin_notes' => 'Cancelled by user',
            'resolved_at' => now()
        ]);

        return redirect()->route('ratings.contests.index')
            ->with('success', 'Contest has been cancelled.');
    }
}
