<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserTypeChangeRequest;
use App\Models\User;

class UserTypeChangeRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /**
     * Display a listing of user type change requests.
     */
    public function index(Request $request)
    {
        $query = UserTypeChangeRequest::with(['user', 'currentUserType', 'requestedUserType']);

        // Filter by status if provided
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search by user name or email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $requests = $query->latest()->paginate(20);

        $stats = [
            'pending' => UserTypeChangeRequest::where('status', 'pending')->count(),
            'approved' => UserTypeChangeRequest::where('status', 'approved')->count(),
            'rejected' => UserTypeChangeRequest::where('status', 'rejected')->count(),
            'cancelled' => UserTypeChangeRequest::where('status', 'cancelled')->count(),
        ];

        return view('admin.user-type-change-requests.index', compact('requests', 'stats'));
    }

    /**
     * Display the specified user type change request.
     */
    public function show(UserTypeChangeRequest $userTypeChangeRequest)
    {
        $userTypeChangeRequest->load(['user', 'currentUserType', 'requestedUserType', 'reviewedBy']);
        
        return view('admin.user-type-change-requests.show', compact('userTypeChangeRequest'));
    }

    /**
     * Approve a user type change request.
     */
    public function approve(Request $request, UserTypeChangeRequest $userTypeChangeRequest)
    {
        if ($userTypeChangeRequest->status !== 'pending') {
            return redirect()->back()
                ->with('error', 'Only pending requests can be approved.');
        }

        $request->validate([
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        // Update the user's type
        $user = $userTypeChangeRequest->user;
        $user->update([
            'user_type_id' => $userTypeChangeRequest->requested_user_type_id,
        ]);

        // Update the request status
        $userTypeChangeRequest->update([
            'status' => 'approved',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'admin_notes' => $request->admin_notes,
        ]);

        return redirect()->route('admin.user-type-change-requests.index')
            ->with('success', "User type change request approved. {$user->name}'s account type has been updated.");
    }

    /**
     * Reject a user type change request.
     */
    public function reject(Request $request, UserTypeChangeRequest $userTypeChangeRequest)
    {
        if ($userTypeChangeRequest->status !== 'pending') {
            return redirect()->back()
                ->with('error', 'Only pending requests can be rejected.');
        }

        $request->validate([
            'admin_notes' => 'required|string|min:10|max:1000',
        ]);

        // Update the request status
        $userTypeChangeRequest->update([
            'status' => 'rejected',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'admin_notes' => $request->admin_notes,
        ]);

        return redirect()->route('admin.user-type-change-requests.index')
            ->with('success', 'User type change request rejected.');
    }

    /**
     * Bulk approve selected requests.
     */
    public function bulkApprove(Request $request)
    {
        $request->validate([
            'request_ids' => 'required|array',
            'request_ids.*' => 'exists:user_type_change_requests,id',
        ]);

        $requests = UserTypeChangeRequest::whereIn('id', $request->request_ids)
            ->where('status', 'pending')
            ->with('user')
            ->get();

        $approved = 0;

        foreach ($requests as $changeRequest) {
            // Update the user's type
            $changeRequest->user->update([
                'user_type_id' => $changeRequest->requested_user_type_id,
            ]);

            // Update the request status
            $changeRequest->update([
                'status' => 'approved',
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
                'admin_notes' => 'Bulk approved by admin.',
            ]);

            $approved++;
        }

        return redirect()->route('admin.user-type-change-requests.index')
            ->with('success', "Successfully approved {$approved} user type change requests.");
    }

    /**
     * Bulk reject selected requests.
     */
    public function bulkReject(Request $request)
    {
        $request->validate([
            'request_ids' => 'required|array',
            'request_ids.*' => 'exists:user_type_change_requests,id',
            'bulk_admin_notes' => 'required|string|min:10|max:1000',
        ]);

        $rejected = UserTypeChangeRequest::whereIn('id', $request->request_ids)
            ->where('status', 'pending')
            ->update([
                'status' => 'rejected',
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
                'admin_notes' => $request->bulk_admin_notes,
            ]);

        return redirect()->route('admin.user-type-change-requests.index')
            ->with('success', "Successfully rejected {$rejected} user type change requests.");
    }
}
