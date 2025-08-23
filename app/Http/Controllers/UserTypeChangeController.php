<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\UserTypeChangeRequest;
use App\Models\UserType;

class UserTypeChangeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the form for requesting a user type change.
     */
    public function create()
    {
        $user = Auth::user();
        
        // Check if user already has a pending request
        if ($user->hasPendingUserTypeChangeRequest()) {
            return redirect()->route('dashboard')
                ->with('warning', 'You already have a pending user type change request.');
        }

        $userTypes = UserType::where('id', '!=', $user->user_type_id)->get();
        $pendingRequest = $user->getPendingUserTypeChangeRequest();

        return view('user-type-change.create', compact('userTypes', 'pendingRequest'));
    }

    /**
     * Store a new user type change request.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        // Check if user can change their user type
        if ($user->canChangeUserType()) {
            return redirect()->route('dashboard')
                ->with('error', 'You can change your user type directly.');
        }

        // Check if user already has a pending request
        if ($user->hasPendingUserTypeChangeRequest()) {
            return redirect()->route('dashboard')
                ->with('warning', 'You already have a pending user type change request.');
        }

        $request->validate([
            'requested_user_type_id' => 'required|exists:user_types,id|different:' . $user->user_type_id,
            'reason' => 'required|string|min:10|max:1000',
            'supporting_documents.*' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120', // 5MB max
        ]);

        $supportingDocuments = [];

        // Handle file uploads
        if ($request->hasFile('supporting_documents')) {
            foreach ($request->file('supporting_documents') as $file) {
                $path = $file->store('user-type-change-documents', 'private');
                $supportingDocuments[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                ];
            }
        }

        // Create the change request
        $changeRequest = $user->requestUserTypeChange(
            $request->requested_user_type_id,
            $request->reason,
            $supportingDocuments
        );

        return redirect()->route('dashboard')
            ->with('success', 'Your user type change request has been submitted and is pending admin approval.');
    }

    /**
     * Show the user's current change request status.
     */
    public function show()
    {
        $user = Auth::user();
        $pendingRequest = $user->getPendingUserTypeChangeRequest();
        $allRequests = $user->userTypeChangeRequests()->with(['currentUserType', 'requestedUserType'])->latest()->get();

        return view('user-type-change.show', compact('pendingRequest', 'allRequests'));
    }

    /**
     * Cancel a pending user type change request.
     */
    public function cancel()
    {
        $user = Auth::user();
        $pendingRequest = $user->getPendingUserTypeChangeRequest();

        if (!$pendingRequest) {
            return redirect()->route('dashboard')
                ->with('error', 'No pending user type change request found.');
        }

        $pendingRequest->update(['status' => 'cancelled']);

        return redirect()->route('dashboard')
            ->with('success', 'Your user type change request has been cancelled.');
    }

    /**
     * Download a supporting document.
     */
    public function downloadDocument(UserTypeChangeRequest $changeRequest, $documentIndex)
    {
        $user = Auth::user();

        // Check if user owns this request or is admin
        if ($changeRequest->user_id !== $user->id && !$user->isAdmin()) {
            abort(403, 'Unauthorized access to document.');
        }

        $documents = $changeRequest->supporting_documents ?? [];
        
        if (!isset($documents[$documentIndex])) {
            abort(404, 'Document not found.');
        }

        $document = $documents[$documentIndex];
        
        if (!Storage::disk('private')->exists($document['path'])) {
            abort(404, 'Document file not found.');
        }

        return Storage::disk('private')->download($document['path'], $document['name']);
    }
}
