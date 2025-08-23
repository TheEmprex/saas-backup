<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserProfile;
use App\Models\UserType;
use App\Models\EarningsVerification;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserProfileController extends Controller
{
    public function show($userId = null)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $user->load(['userProfile', 'userType', 'contractReviewsReceived.reviewer', 'contractReviewsGiven.reviewedUser']);
        
        // Get the user profile for template compatibility
        $profile = $user->userProfile;
        
        // Calculate profile statistics (similar to MarketplaceController)
        $stats = [
            'average_rating' => $user->contractReviewsReceived->avg('rating') ?? 0.0,
            'total_reviews' => $user->contractReviewsReceived->count(),
            'jobs_completed' => $user->contractReviewsReceived->count(), // Using reviews as proxy for completed jobs
            'response_time' => 'Within 24h', // Default response time
            'profile_complete' => $this->isProfileComplete($user),
            'profile_completeness_percentage' => $this->calculateProfileCompleteness($user)
        ];
        
        return view('theme::profile.show', compact('user', 'profile', 'stats'));
    }
    
    public function publicProfile(User $user)
    {
        // Load user with profile and user type
        $user->load(['userProfile', 'userType', 'contractReviewsReceived.reviewer']);
        
        // Only show public profiles for users with complete profiles
        if (!$user->userProfile || !$user->userProfile->bio) {
            abort(404, 'Profile not found or incomplete');
        }
        
        // Load marketplace reviews received by this user using the ReviewHelper trait
        $marketplaceReviews = $user->getProfileReviews(10);
        
        return view('theme::profile.public', compact('user', 'marketplaceReviews'));
    }
    
    public function edit()
    {
        $user = Auth::user()->load(['userProfile', 'userType']);
        $userTypes = UserType::where('active', true)->get();
        
        return view('theme::profile.edit', compact('user', 'userTypes'));
    }
    
    public function update(Request $request)
    {
        $user = Auth::user();
        
        // Validate basic user info
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'user_type_id' => 'nullable|exists:user_types,id',
            'bio' => 'nullable|string|max:1000',
            'location' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
            'phone' => 'nullable|string|max:20',
            'experience_years' => 'nullable|integer|min:0|max:50',
            'typing_speed_wpm' => 'nullable|integer|min:10|max:200',
            'languages' => 'nullable|array',
            'skills' => 'nullable|array',
            'services' => 'nullable|array',
            'availability' => 'nullable|string|max:500',
            'hourly_rate' => 'nullable|numeric|min:0',
            'preferred_rate_type' => 'nullable|in:hourly,fixed,commission',
            'portfolio_url' => 'nullable|url|max:500',
            'linkedin_url' => 'nullable|url|max:500',
            'portfolio_links' => 'nullable|array',
            'is_available' => 'nullable|boolean',
            'response_time' => 'nullable|in:within_an_hour,within_a_few_hours,within_a_day,within_a_few_days',
            'avatar' => 'nullable|image|max:2048',
            // Agency-specific fields
            'monthly_revenue' => 'nullable|in:0-5k,5-10k,10-25k,25-50k,50-100k,100-250k,250k-1m,1m+',
            'traffic_types' => 'nullable|array',
            'average_ltv' => 'nullable|numeric|min:0',
            // VA/Chatter work hours
            'timezone' => 'nullable|string|max:50',
            'work_hours' => 'nullable|array',
        ]);
        
        // Update user basic info
        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'user_type_id' => $validated['user_type_id'] ?? $user->user_type_id,
        ]);
        
        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            $imageService = new ImageService();
            $avatarPath = $imageService->uploadAndResizeProfilePicture(
                $request->file('avatar'),
                $user->avatar
            );
            $user->update(['avatar' => $avatarPath]);
        }
        
        // Update or create user profile
        $profileData = [
            'user_type_id' => $user->user_type_id ?? 1, // Default to first user type if not set
            'bio' => $validated['bio'] ?? null,
            'location' => $validated['location'] ?? null,
            'website' => $validated['website'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'experience_years' => $validated['experience_years'] ?? null,
            'typing_speed_wpm' => $validated['typing_speed_wpm'] ?? null,
            'languages' => isset($validated['languages']) && $validated['languages'] ? $validated['languages'] : null,
            'skills' => isset($validated['skills']) && $validated['skills'] ? $validated['skills'] : null,
            'services' => isset($validated['services']) && $validated['services'] ? $validated['services'] : null,
            'availability' => $validated['availability'] ?? null,
            'hourly_rate' => $validated['hourly_rate'] ?? null,
            'preferred_rate_type' => $validated['preferred_rate_type'] ?? null,
            'portfolio_url' => $validated['portfolio_url'] ?? null,
            'linkedin_url' => $validated['linkedin_url'] ?? null,
            'portfolio_links' => isset($validated['portfolio_links']) && $validated['portfolio_links'] ? $validated['portfolio_links'] : null,
            'is_available' => $validated['is_available'] ?? true,
            'response_time' => $validated['response_time'] ?? null,
        ];
        
        // Add agency-specific fields
        if ($user->isAgency()) {
            $profileData['monthly_revenue'] = $validated['monthly_revenue'] ?? null;
            $profileData['traffic_types'] = isset($validated['traffic_types']) && $validated['traffic_types'] ? $validated['traffic_types'] : null;
            $profileData['average_ltv'] = $validated['average_ltv'] ?? null;
        }
        
        // Add VA/Chatter work hours
        if (!$user->isAgency() && ($user->isVa() || $user->isChatter())) {
            $profileData['timezone'] = $validated['timezone'] ?? null;
            $profileData['work_hours'] = isset($validated['work_hours']) && $validated['work_hours'] ? $validated['work_hours'] : null;
        }
        
        $user->userProfile()->updateOrCreate(
            ['user_id' => $user->id],
            $profileData
        );
        
        return redirect()->route('profile.show')
            ->with('success', 'Profile updated successfully!');
    }
    
    public function kyc()
    {
        $user = Auth::user()->load(['userProfile', 'userType', 'kycVerification']);
        
        // If user already has KYC submitted, redirect to view it
        if ($user->hasKycSubmitted()) {
            return redirect()->route('kyc.show', $user->kycVerification->id);
        }
        
        return redirect()->route('kyc.create');
    }
    
    public function submitKyc(Request $request)
    {
        // Redirect to proper KYC controller
        return redirect()->route('kyc.create')
            ->with('info', 'Please use the dedicated KYC verification form.');
    }
    
    public function typingTest()
    {
        $user = Auth::user()->load(['userProfile']);
        
        return view('theme::profile.typing-test', compact('user'));
    }
    
    public function submitTypingTest(Request $request)
    {
        $validated = $request->validate([
            'wpm' => 'required|integer|min:10|max:200',
            'accuracy' => 'required|integer|min:50|max:100',
            'time_taken' => 'required|integer|min:30|max:300',
        ]);
        
        $user = Auth::user();
        $user->userProfile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'user_type_id' => $user->user_type_id ?? 1,
                'typing_speed_wpm' => $validated['wpm'],
                'typing_accuracy' => $validated['accuracy'],
                'typing_test_taken_at' => now(),
            ]
        );
        
        return redirect()->route('profile.show')
            ->with('success', 'Typing test completed! Your WPM: ' . $validated['wpm']);
    }
    
    public function earningsVerification()
    {
        $user = Auth::user()->load(['userProfile', 'userType', 'earningsVerification']);
        
        // Check if user is an agency
        if (!$user->isAgency()) {
            return redirect()->route('dashboard')
                ->with('error', 'Earnings verification is only for agencies.');
        }
        
        return view('theme::profile.earnings-verification', compact('user'));
    }
    
    private function isProfileComplete(User $user)
    {
        $profile = $user->userProfile;
        
        if (!$profile) {
            return false;
        }
        
        // Check required fields for a complete profile
        $requiredFields = [
            'bio',
            'location',
        ];
        
        foreach ($requiredFields as $field) {
            if (empty($profile->$field)) {
                return false;
            }
        }
        
        // Check if user has avatar
        if (!$user->avatar) {
            return false;
        }
        
        // Check if user has skills or services
        if (empty($profile->skills) && empty($profile->services)) {
            return false;
        }
        
        return true;
    }
    
    private function calculateProfileCompleteness(User $user)
    {
        $profile = $user->userProfile;
        
        if (!$profile) {
            return 10; // Base 10% for having a user account
        }
        
        $score = 10; // Base score
        
        // Avatar (15%)
        if ($user->avatar) {
            $score += 15;
        }
        
        // Bio (20%)
        if (!empty($profile->bio)) {
            $score += 20;
        }
        
        // Location (10%)
        if (!empty($profile->location)) {
            $score += 10;
        }
        
        // Skills (15%)
        if (!empty($profile->skills)) {
            $score += 15;
        }
        
        // Services (10%)
        if (!empty($profile->services)) {
            $score += 10;
        }
        
        // Experience years (5%)
        if (!empty($profile->experience_years)) {
            $score += 5;
        }
        
        // Hourly rate (5%)
        if (!empty($profile->hourly_rate)) {
            $score += 5;
        }
        
        // Portfolio URL (5%)
        if (!empty($profile->portfolio_url)) {
            $score += 5;
        }
        
        // Languages (5%)
        if (!empty($profile->languages)) {
            $score += 5;
        }
        
        return min($score, 100);
    }
    
    public function submitEarningsVerification(Request $request)
    {
        $user = Auth::user();
        
        // Check if user is an agency
        if (!$user->isAgency()) {
            return redirect()->route('dashboard')
                ->with('error', 'Earnings verification is only for agencies.');
        }
        
        $validated = $request->validate([
            'platform_name' => 'required|string|max:255',
            'platform_username' => 'required|string|max:255',
            'monthly_earnings' => 'required|numeric|min:0|max:999999.99',
            'earnings_screenshot' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB max
            'profile_screenshot' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'additional_notes' => 'nullable|string|max:1000',
        ]);
        
        try {
            // Handle file uploads
            $earningsScreenshotPath = $request->file('earnings_screenshot')
                ->store('earnings-verifications', 'private');
            
            $profileScreenshotPath = null;
            if ($request->hasFile('profile_screenshot')) {
                $profileScreenshotPath = $request->file('profile_screenshot')
                    ->store('earnings-verifications', 'private');
            }
            
            // Create or update earnings verification
            $verification = $user->earningsVerification()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'platform_name' => $validated['platform_name'],
                    'platform_username' => $validated['platform_username'],
                    'monthly_earnings' => $validated['monthly_earnings'],
                    'earnings_screenshot_path' => $earningsScreenshotPath,
                    'profile_screenshot_path' => $profileScreenshotPath,
                    'additional_notes' => $validated['additional_notes'] ?? null,
                    'status' => 'pending',
                    'verified_at' => null,
                ]
            );
            
            return redirect()->route('profile.earnings-verification')
                ->with('success', 'Earnings verification submitted successfully! We will review your submission within 24-48 hours.');
                
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Earnings verification submission failed: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'There was an error submitting your verification. Please try again.')
                ->withInput();
        }
    }
}
