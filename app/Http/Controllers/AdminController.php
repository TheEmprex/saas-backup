<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\EarningsVerification;
use App\Models\KycVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (! auth()->user()->isAdmin()) {
                abort(403, 'Unauthorized access');
            }

            return $next($request);
        });
    }

    /**
     * Admin dashboard
     */
    public function dashboard()
    {
        $kycPendingCount = KycVerification::query->where('status', 'pending')->count();
        $earningsPendingCount = EarningsVerification::query->where('status', 'pending')->count();

        $stats = [
            'kyc_pending' => $kycPendingCount,
            'kyc_approved' => KycVerification::query->where('status', 'approved')->count(),
            'kyc_rejected' => KycVerification::query->where('status', 'rejected')->count(),
            'earnings_pending' => $earningsPendingCount,
            'earnings_approved' => EarningsVerification::query->where('status', 'approved')->count(),
            'earnings_rejected' => EarningsVerification::query->where('status', 'rejected')->count(),
        ];

        return view('admin.dashboard', ['stats' => $stats]);
    }

    /**
     * List all KYC verifications
     */
    public function kycVerifications(Request $request)
    {
        $query = KycVerification::with('user')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $verifications = $query->paginate(20);

        return view('admin.kyc.index', ['verifications' => $verifications]);
    }

    /**
     * Show specific KYC verification
     */
    public function showKycVerification(KycVerification $verification)
    {
        $verification->load('user');

        return view('admin.kyc.show', ['verification' => $verification]);
    }

    /**
     * Update KYC verification status
     */
    public function updateKycStatus(Request $request, KycVerification $verification)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected',
            'rejection_reason' => 'nullable|string|max:1000',
        ]);

        $verification->update([
            'status' => $request->status,
            'rejection_reason' => $request->status === 'rejected' ? $request->rejection_reason : null,
            'verified_at' => $request->status === 'approved' ? now() : null,
        ]);

        return redirect()->route('admin.kyc.show', $verification)
            ->with('success', 'KYC verification status updated successfully.');
    }

    /**
     * List all earnings verifications
     */
    public function earningsVerifications(Request $request)
    {
        $query = EarningsVerification::with('user')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $verifications = $query->paginate(20);

        return view('admin.earnings.index', ['verifications' => $verifications]);
    }

    /**
     * Show specific earnings verification
     */
    public function showEarningsVerification(EarningsVerification $verification)
    {
        $verification->load('user');

        return view('admin.earnings.show', ['verification' => $verification]);
    }

    /**
     * Update earnings verification status
     */
    public function updateEarningsStatus(Request $request, EarningsVerification $verification)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected',
            'rejection_reason' => 'nullable|string|max:1000',
        ]);

        // Only update specific fields, don't affect file paths
        $verification->status = $request->status;
        $verification->rejection_reason = $request->status === 'rejected' ? $request->rejection_reason : null;
        $verification->verified_at = $request->status === 'approved' ? now() : null;
        $verification->save();

        return redirect()->route('admin.earnings.show', $verification)
            ->with('success', 'Earnings verification status updated successfully.');
    }

    /**
     * Download verification files
     */
    public function downloadEarningsFile(EarningsVerification $verification, $type)
    {
        $filePath = match ($type) {
            'earnings_screenshot' => $verification->earnings_screenshot_path,
            'profile_screenshot' => $verification->profile_screenshot_path,
            default => null
        };

        if (! $filePath || ! Storage::disk('private')->exists($filePath)) {
            abort(404, 'File not found');
        }

        return Storage::disk('private')->download($filePath);
    }

    /**
     * Preview verification files (serve as image)
     */
    public function previewEarningsFile(EarningsVerification $verification, $type)
    {
        $filePath = match ($type) {
            'earnings_screenshot' => $verification->earnings_screenshot_path,
            'profile_screenshot' => $verification->profile_screenshot_path,
            default => null
        };

        if (! $filePath || ! Storage::disk('private')->exists($filePath)) {
            abort(404, 'File not found');
        }

        $file = Storage::disk('private')->get($filePath);
        $mimeType = Storage::disk('private')->mimeType($filePath);

        return response($file, 200, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline',
        ]);
    }
}
