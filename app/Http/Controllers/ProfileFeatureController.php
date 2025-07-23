<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProfileFeatureController extends Controller
{
    /**
     * Show the profile feature payment page.
     */
    public function show()
    {
        $user = Auth::user();
        $profile = $user->userProfile;
        
        if (!$profile) {
            return redirect()->route('profile.edit')
                ->with('error', 'Please complete your profile first.');
        }
        
        // Check if profile is already featured and active
        $isFeaturedActive = $profile->is_featured && 
                           $profile->featured_until && 
                           $profile->featured_until->isFuture();
        
        $featuredCost = 5.00;
        
        return view('theme::profile.feature', compact('user', 'profile', 'isFeaturedActive', 'featuredCost'));
    }
    
    /**
     * Process the profile feature payment.
     */
    public function process(Request $request)
    {
        $user = Auth::user();
        $profile = $user->userProfile;
        
        if (!$profile) {
            return redirect()->route('profile.edit')
                ->with('error', 'Please complete your profile first.');
        }
        
        // Check if profile is already featured and active
        $isFeaturedActive = $profile->is_featured && 
                           $profile->featured_until && 
                           $profile->featured_until->isFuture();
                           
        if ($isFeaturedActive) {
            return redirect()->route('profile.show')
                ->with('error', 'Your profile is already featured.');
        }
        
        $featuredCost = 5.00;
        
        try {
            DB::transaction(function () use ($profile, $featuredCost) {
                // For now, we'll simulate payment processing
                // In a real implementation, you would integrate with Stripe or another payment processor
                $paymentId = 'demo_profile_feature_' . time();
                
                // Update profile with feature status
                $profile->update([
                    'is_featured' => true,
                    'featured_until' => Carbon::now()->addDays(30), // Featured for 30 days
                    'featured_payment_amount' => $featuredCost,
                    'featured_payment_id' => $paymentId,
                    'featured_paid_at' => now(),
                ]);
            });
            
            return redirect()->route('profile.show')
                ->with('success', 'Your profile has been featured successfully! It will be highlighted for 30 days.');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to process payment. Please try again.');
        }
    }
    
    /**
     * Check if user can feature their profile.
     */
    public function canFeature()
    {
        $user = Auth::user();
        $profile = $user->userProfile;
        
        if (!$profile) {
            return response()->json([
                'can_feature' => false,
                'reason' => 'Profile not found'
            ]);
        }
        
        $isFeaturedActive = $profile->is_featured && 
                           $profile->featured_until && 
                           $profile->featured_until->isFuture();
                           
        return response()->json([
            'can_feature' => !$isFeaturedActive,
            'is_featured' => $profile->is_featured,
            'featured_until' => $profile->featured_until?->toDateTimeString(),
            'days_remaining' => $isFeaturedActive ? 
                $profile->featured_until->diffInDays(Carbon::now()) : 0
        ]);
    }
}
