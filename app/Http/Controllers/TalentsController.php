<?php

namespace App\Http\Controllers;

use App\Models\UserProfile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class TalentsController extends Controller
{
    /**
     * Display a listing of the talents.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Check if the current user meets marketplace requirements if they want to appear
        $canAppear = $user && $user->meetsMarketplaceRequirements();

        if (!$canAppear && $user) {
            return redirect()->route('training.index')->with(
                'warning', 'Complete email verification and KYC (if required for your user type) to appear as a talent.'
            );
        }

        // Filter talents based on simplified requirements (only email verification and KYC if required)
        $talents = UserProfile::with(['user', 'user.userType'])
            ->where('is_active', true)
            ->where('is_verified', true)
            ->whereHas('user', function ($q) {
                $q->whereNotNull('email_verified_at')
                  ->where(function($subQ) {
                      // Either KYC is not required for the user type, or it's verified
                      $subQ->whereDoesntHave('userType', function($typeQ) {
                          $typeQ->where('requires_kyc', true);
                      })
                      ->orWhere(function($kycQ) {
                          $kycQ->whereHas('userType', function($typeQ) {
                              $typeQ->where('requires_kyc', true);
                          })->whereHas('kycVerification', function($verQ) {
                              $verQ->where('status', 'approved');
                          });
                      });
                  });
            })
            ->paginate(10);

        return view('theme::talents.index', compact('talents'));
    }
}

