<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserType;
use App\Services\ImageService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserProfileController extends Controller
{
    public function show($userId = null)
    {
        $user = Auth::user();

        if (! $user) {
            return redirect()->route('login');
        }

        $user->load(['userProfile', 'userType', 'contractReviewsReceived.reviewer', 'contractReviewsGiven.reviewedUser']);

        return view('theme::profile.show', ['user' => $user]);
    }

    public function edit()
    {
        $user = Auth::user()->load(['userProfile', 'userType']);
        $userTypes = UserType::where('active', true)->get();

        return view('theme::profile.edit', ['user' => $user, 'userTypes' => $userTypes]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        // Validate basic user info
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'user_type_id' => 'nullable|exists:user_types,id',
            'bio' => 'nullable|string|max:1000',
            'location' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
            'phone' => 'nullable|string|max:20',
            'experience_years' => 'nullable|integer|min:0|max:50',
            'typing_speed_wpm' => 'nullable|integer|min:10|max:200',
            'languages' => 'nullable|array',
            'skills' => 'nullable|array',
            'availability' => 'nullable|string|max:500',
            'hourly_rate' => 'nullable|numeric|min:0',
            'preferred_rate_type' => 'nullable|in:hourly,fixed,commission',
            'portfolio_url' => 'nullable|url|max:500',
            'linkedin_url' => 'nullable|url|max:500',
            'avatar' => 'nullable|image|max:2048',
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
            'languages' => isset($validated['languages']) && $validated['languages'] ? json_encode($validated['languages']) : null,
            'skills' => isset($validated['skills']) && $validated['skills'] ? json_encode($validated['skills']) : null,
            'availability' => $validated['availability'] ?? null,
            'hourly_rate' => $validated['hourly_rate'] ?? null,
            'preferred_rate_type' => $validated['preferred_rate_type'] ?? null,
            'portfolio_url' => $validated['portfolio_url'] ?? null,
            'linkedin_url' => $validated['linkedin_url'] ?? null,
        ];

        $user->userProfile()->updateOrCreate(
            ['user_id' => $user->id],
            $profileData
        );

        return redirect()->route('profile.show')
            ->with('success', 'Profile updated successfully!');
    }

    public function publicProfile(User $user)
    {
        // Load user with profile and user type
        $user->load(['userProfile', 'userType', 'contractReviewsReceived.reviewer']);

        // Only show public profiles for users with complete profiles
        if (! $user->userProfile || ! $user->userProfile->bio) {
            abort(404, 'Profile not found or incomplete');
        }

        return view('theme::profile.public', ['user' => $user]);
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

        return view('theme::profile.typing-test', ['user' => $user]);
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
            ->with('success', 'Typing test completed! Your WPM: '.$validated['wpm']);
    }

    public function earningsVerification()
    {
        $user = Auth::user()->load(['userProfile', 'userType', 'earningsVerification']);

        // Check if user is an agency
        if (! $user->isAgency()) {
            return redirect()->route('dashboard')
                ->with('error', 'Earnings verification is only for agencies.');
        }

        return view('theme::profile.earnings-verification', ['user' => $user]);
    }

    public function submitEarningsVerification(Request $request)
    {
        $user = Auth::user();

        // Check if user is an agency
        if (! $user->isAgency()) {
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

        } catch (Exception $exception) {
            // Log the error for debugging
            Log::error('Earnings verification submission failed: '.$exception->getMessage());

            return redirect()->back()
                ->with('error', 'There was an error submitting your verification. Please try again.')
                ->withInput();
        }
    }
}
