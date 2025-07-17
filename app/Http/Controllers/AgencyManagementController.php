<?php

namespace App\Http\Controllers;

use App\Models\EmploymentContract;
use App\Models\ShiftReview;
use App\Models\WorkShift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AgencyManagementController extends Controller
{
    public function myEmployees()
    {
        $agency = auth()->user();
        $employees = $agency->activeEmployees()->with('chatter')->get();

        return view('agency.employees.index', compact('employees'));
    }

    public function terminateContract(EmploymentContract $contract, Request $request)
    {
        $request->validate([
            'termination_reason' => 'required|string|max:255',
        ]);

        if ($contract->agency_id !== auth()->id() || !$contract->isActive()) {
            abort(403, 'Unauthorized action.');
        }

        $contract->update([
            'status' => 'terminated',
            'terminated_at' => now(),
            'termination_reason' => $request->termination_reason,
            'terminated_by' => auth()->id(),
        ]);

        return redirect()->route('agency.employees.index')->with('status', 'Contract terminated successfully.');
    }

    public function reviewShift(WorkShift $shift, Request $request)
    {
        $request->validate([
            'overall_rating' => 'required|integer|min:1|max:5',
            'review_comment' => 'nullable|string',
        ]);

        if ($shift->agency_id !== auth()->id() || !$shift->isCompleted()) {
            abort(403, 'Unauthorized action.');
        }

        $review = ShiftReview::create([
            'work_shift_id' => $shift->id,
            'employment_contract_id' => $shift->employment_contract_id,
            'reviewer_id' => auth()->id(),
            'chatter_id' => $shift->chatter_id,
            'overall_rating' => $request->overall_rating,
            'review_comment' => $request->review_comment,
        ]);

        $shift->update(['reviewed_by_agency' => true, 'reviewed_at' => now()]);
        $shift->employmentContract->updateAverageRating();

        return redirect()->route('agency.employees.index')->with('status', 'Shift reviewed successfully.');
    }
}
