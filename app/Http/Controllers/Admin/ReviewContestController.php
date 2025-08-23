<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ReviewContest;
use App\Models\Rating;

class ReviewContestController extends Controller
{
    /**
     * Display a listing of contests.
     */
    public function index(Request $request)
    {
        $query = ReviewContest::with(['rating.rater', 'rating.rated', 'contestedBy', 'reviewedBy']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search by user
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('contestedBy', function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        $contests = $query->orderBy('created_at', 'desc')->paginate(20);

        $stats = [
            'total' => ReviewContest::count(),
            'pending' => ReviewContest::where('status', 'pending')->count(),
            'approved' => ReviewContest::where('status', 'approved')->count(),
            'rejected' => ReviewContest::where('status', 'rejected')->count(),
        ];

        return view('admin.contests.index', compact('contests', 'stats'));
    }

    /**
     * Display the specified contest.
     */
    public function show(ReviewContest $contest)
    {
        $contest->load([
            'rating.rater', 
            'rating.rated', 
            'contestedBy', 
            'reviewedBy'
        ]);

        return view('admin.contests.show', compact('contest'));
    }

    /**
     * Approve the contest (remove the rating).
     */
    public function approve(Request $request, ReviewContest $contest)
    {
        $request->validate([
            'admin_notes' => 'nullable|string|max:500'
        ]);

        // Remove the contested rating
        $contest->rating->delete();

        $contest->update([
            'status' => 'approved',
            'reviewed_by' => auth()->id(),
            'admin_notes' => $request->admin_notes,
            'resolved_at' => now()
        ]);

        return redirect()->route('admin.contests.index')
            ->with('success', 'Contest approved and rating removed.');
    }

    /**
     * Reject the contest (keep the rating).
     */
    public function reject(Request $request, ReviewContest $contest)
    {
        $request->validate([
            'admin_notes' => 'required|string|max:500'
        ]);

        $contest->update([
            'status' => 'rejected',
            'reviewed_by' => auth()->id(),
            'admin_notes' => $request->admin_notes,
            'resolved_at' => now()
        ]);

        return redirect()->route('admin.contests.index')
            ->with('success', 'Contest rejected.');
    }

    /**
     * Bulk approve contests.
     */
    public function bulkApprove(Request $request)
    {
        $request->validate([
            'contest_ids' => 'required|array',
            'contest_ids.*' => 'exists:review_contests,id'
        ]);

        $contests = ReviewContest::whereIn('id', $request->contest_ids)
            ->where('status', 'pending')
            ->get();

        foreach ($contests as $contest) {
            // Remove the contested rating
            $contest->rating->delete();
            
            $contest->update([
                'status' => 'approved',
                'reviewed_by' => auth()->id(),
                'admin_notes' => 'Bulk approved',
                'resolved_at' => now()
            ]);
        }

        return redirect()->back()
            ->with('success', count($contests) . ' contests approved.');
    }

    /**
     * Bulk reject contests.
     */
    public function bulkReject(Request $request)
    {
        $request->validate([
            'contest_ids' => 'required|array',
            'contest_ids.*' => 'exists:review_contests,id',
            'admin_notes' => 'required|string|max:500'
        ]);

        $contests = ReviewContest::whereIn('id', $request->contest_ids)
            ->where('status', 'pending')
            ->get();

        foreach ($contests as $contest) {
            $contest->update([
                'status' => 'rejected',
                'reviewed_by' => auth()->id(),
                'admin_notes' => $request->admin_notes,
                'resolved_at' => now()
            ]);
        }

        return redirect()->back()
            ->with('success', count($contests) . ' contests rejected.');
    }
}
