<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ContractApprovalController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show pending contracts for the authenticated user
     */
    public function index(): View
    {
        $pendingContracts = Contract::where('contractor_id', auth()->id())
            ->where('approval_status', 'pending')
            ->with(['employer', 'jobPost'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('contracts.approvals.index', compact('pendingContracts'));
    }

    /**
     * Show a specific contract for approval
     */
    public function show(Contract $contract): View
    {
        // Check if user can approve this contract
        if (!$contract->canBeApprovedBy(auth()->user())) {
            abort(403, 'You cannot approve this contract.');
        }

        $contract->load(['employer', 'jobPost']);

        return view('contracts.approvals.show', compact('contract'));
    }

    /**
     * Accept a contract
     */
    public function accept(Contract $contract): RedirectResponse
    {
        if (!$contract->canBeApprovedBy(auth()->user())) {
            return redirect()->back()->with('error', 'You cannot approve this contract.');
        }

        if ($contract->accept()) {
            return redirect()->route('contracts.approvals.index')
                ->with('success', 'Contract accepted successfully! The contract is now active.');
        }

        return redirect()->back()->with('error', 'Failed to accept the contract.');
    }

    /**
     * Reject a contract
     */
    public function reject(Request $request, Contract $contract): RedirectResponse
    {
        if (!$contract->canBeApprovedBy(auth()->user())) {
            return redirect()->back()->with('error', 'You cannot reject this contract.');
        }

        $validated = $request->validate([
            'rejection_reason' => 'nullable|string|max:1000'
        ]);

        if ($contract->reject($validated['rejection_reason'] ?? null)) {
            return redirect()->route('contracts.approvals.index')
                ->with('success', 'Contract rejected successfully.');
        }

        return redirect()->back()->with('error', 'Failed to reject the contract.');
    }
}
