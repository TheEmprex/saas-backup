<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Wave\Facades\Wave;
use App\Http\Controllers\MarketplaceController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\UserTypeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\KycController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\CustomAuthController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\JobPaymentController;
use App\Http\Controllers\AgencyManagementController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ContractApprovalController;
use App\Http\Controllers\EmailVerificationController;

// Wave routes first
Wave::routes();

// Override the Wave login route to redirect to our custom login
Route::get('/login', function() {
    return redirect()->route('custom.login');
})->name('custom.login.redirect');

// Override the default register route to redirect to our custom register
Route::get('/register', function() {
    return redirect()->route('custom.register');
})->name('custom.register.redirect');

// Redirect home to marketplace
Route::get('/', function () {
    return redirect()->route('marketplace.index');
})->name('home');

// Marketplace routes
Route::get('/marketplace', [MarketplaceController::class, 'index'])->name('marketplace.index');
Route::get('/marketplace/jobs', [MarketplaceController::class, 'jobs'])->name('marketplace.jobs');
Route::get('/marketplace/profiles', [MarketplaceController::class, 'profiles'])->name('marketplace.profiles');
Route::get('/marketplace/chatters', [MarketplaceController::class, 'chatters'])->name('marketplace.chatters');
Route::get('/marketplace/agencies', [MarketplaceController::class, 'agencies'])->name('marketplace.agencies');

// Marketplace job creation routes (must be before parameterized routes)
Route::get('/marketplace/jobs/create', [JobController::class, 'create'])->name('marketplace.jobs.create')->middleware(['auth', 'can.post.jobs']);

// Profile review routes (must be before parameterized routes)
Route::get('/marketplace/profiles/{user}/reviews', [MarketplaceController::class, 'profileReviews'])->name('marketplace.profiles.reviews');

// Marketplace parameterized routes (must be after specific routes)
Route::get('/marketplace/jobs/{job}', [MarketplaceController::class, 'jobShow'])->name('marketplace.jobs.show');
Route::get('/marketplace/profiles/{user}', [MarketplaceController::class, 'profileShow'])->name('marketplace.profiles.show');

// Platform routes
Route::get('/platform/analytics', [MarketplaceController::class, 'analytics'])->name('platform.analytics')->middleware('auth');

// Secret admin access route (only accessible via direct URL)
Route::get('/system/admin-access', function () {
    if (auth()->guest() || !auth()->user()->isAdmin()) {
        abort(404);
    }
    return redirect()->route('platform.analytics');
})->name('system.admin-access')->middleware('auth');

// Simple test admin route
Route::get('/admin-test', function () {
    return 'Admin test route works! User: ' . (auth()->check() ? auth()->user()->email : 'Not logged in');
})->name('admin.test');

// Debug route to check user verification status
Route::get('/debug-user', function () {
    if (!auth()->check()) {
        return 'Not logged in';
    }
    
    $user = auth()->user();
    return [
        'user_id' => $user->id,
        'email' => $user->email,
        'user_type' => $user->userType ? $user->userType->name : 'No user type',
        'is_chatter' => $user->isChatter(),
        'is_agency' => $user->isAgency(),
        'is_admin' => $user->isAdmin(),
        'has_kyc_submitted' => $user->hasKycSubmitted(),
        'is_kyc_verified' => $user->isKycVerified(),
        'is_earnings_verified' => $user->isEarningsVerified(),
    ];
})->middleware('auth');


// Admin management routes - comprehensive admin panel
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    // Admin dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // User management
    Route::get('/users', [AdminController::class, 'users'])->name('users.index');
    Route::get('/users/{user}', [AdminController::class, 'showUser'])->name('users.show');
    Route::post('/users/{user}/ban', [AdminController::class, 'banUser'])->name('users.ban');
    Route::post('/users/{user}/unban', [AdminController::class, 'unbanUser'])->name('users.unban');
    Route::post('/users/{user}/verify-email', [AdminController::class, 'verifyUserEmail'])->name('users.verify-email');
    Route::post('/users/{user}/unverify-email', [AdminController::class, 'unverifyUserEmail'])->name('users.unverify-email');
    Route::delete('/users/{user}', [AdminController::class, 'deleteUser'])->name('users.delete');
    Route::post('/users/{user}/impersonate', [AdminController::class, 'impersonateUser'])->name('users.impersonate');
    
    // Stop impersonating
    Route::post('/stop-impersonating', [AdminController::class, 'stopImpersonating'])->name('stop-impersonating');
    
    // KYC verification management
    Route::get('/kyc', [AdminController::class, 'kycVerifications'])->name('kyc.index');
    Route::get('/kyc/{verification}', [AdminController::class, 'showKycVerification'])->name('kyc.show');
    Route::patch('/kyc/{verification}/status', [AdminController::class, 'updateKycStatus'])->name('kyc.update-status');
    Route::get('/kyc/{verification}/download/{type}', [AdminController::class, 'downloadKycFile'])->name('kyc.download');
    Route::get('/kyc/{verification}/preview/{type}', [AdminController::class, 'previewKycFile'])->name('kyc.preview');
    
    // Earnings verification management
    Route::get('/earnings', [AdminController::class, 'earningsVerifications'])->name('earnings.index');
    Route::get('/earnings/{verification}', [AdminController::class, 'showEarningsVerification'])->name('earnings.show');
    Route::patch('/earnings/{verification}/status', [AdminController::class, 'updateEarningsStatus'])->name('earnings.update-status');
    Route::get('/earnings/{verification}/download/{type}', [AdminController::class, 'downloadEarningsFile'])->name('earnings.download');
    Route::get('/earnings/{verification}/preview/{type}', [AdminController::class, 'previewEarningsFile'])->name('earnings.preview');
    
    // Job management
    Route::get('/jobs', [AdminController::class, 'jobs'])->name('jobs.index');
    Route::get('/jobs/{job}', [AdminController::class, 'showJob'])->name('jobs.show');
    Route::delete('/jobs/{job}', [AdminController::class, 'deleteJob'])->name('jobs.delete');
    
    // Message management
    Route::get('/messages', [AdminController::class, 'messages'])->name('messages.index');
    Route::delete('/messages/{message}', [AdminController::class, 'deleteMessage'])->name('messages.delete');
    
    // API endpoints for admin dashboard
    Route::get('/api/stats', [AdminController::class, 'getStats'])->name('api.stats');
});
Route::get('/platform/tools', [MarketplaceController::class, 'tools'])->name('platform.tools');
Route::get('/platform/automation', [MarketplaceController::class, 'automation'])->name('platform.automation');
Route::get('/platform/integrations', [MarketplaceController::class, 'integrations'])->name('platform.integrations');
Route::get('/platform/api', [MarketplaceController::class, 'api'])->name('platform.api');

// Static pages
Route::get('/about', function () {
    return view('theme::about');
})->name('about');

// Temporary test route for authentication bypass
Route::get('/test-login', function () {
    $user = App\Models\User::where('email', 'test@example.com')->first();
    if ($user) {
        Auth::login($user);
        return redirect()->route('marketplace.index')->with('success', 'Logged in as test user');
    }
    return 'Test user not found';
});

// Login as Max (user with contract reviews)
Route::get('/login-max', function () {
    $user = App\Models\User::find(5);
    if ($user) {
        Auth::login($user);
        return redirect()->route('profile.show')->with('success', 'Logged in as Max');
    }
    return 'Max user not found';
});

// Login as Test User (user who has given reviews)
Route::get('/login-test', function () {
    $user = App\Models\User::find(2);
    if ($user) {
        Auth::login($user);
        return redirect()->route('profile.show')->with('success', 'Logged in as Test User');
    }
    return 'Test User not found';
});

// Debug profile route
Route::get('/debug-profile', function () {
    if (!auth()->check()) {
        return 'Not authenticated';
    }
    
    $user = auth()->user();
    $output = [];
    $output[] = 'User: ' . $user->name . ' (ID: ' . $user->id . ')';
    $output[] = 'Email: ' . $user->email;
    $output[] = 'User Type ID: ' . ($user->user_type_id ?? 'NULL');
    
    // Check if user has profile
    $profile = $user->userProfile;
    $output[] = 'Has Profile: ' . ($profile ? 'Yes' : 'No');
    
    // Check if user has user type
    $userType = $user->userType;
    $output[] = 'User Type: ' . ($userType ? $userType->name : 'NULL');
    
    // Check if getProfilePictureUrl method works
    try {
        $profilePicUrl = $user->getProfilePictureUrl();
        $output[] = 'Profile Picture URL: ' . $profilePicUrl;
    } catch (Exception $e) {
        $output[] = 'Profile Picture URL Error: ' . $e->getMessage();
    }
    
    // Try to load the actual controller
    try {
        $controller = new App\Http\Controllers\UserProfileController();
        $response = $controller->show();
        $output[] = 'Controller Response: Success';
    } catch (Exception $e) {
        $output[] = 'Controller Error: ' . $e->getMessage();
    }
    
    return '<pre>' . implode("\n", $output) . '</pre>';
});

// Test public profile for Max
Route::get('/public-max', function () {
    $user = App\Models\User::find(5);
    if ($user) {
        return redirect()->route('profile.public', $user);
    }
    return 'Max user not found';
});
Route::get('/pricing', [SubscriptionController::class, 'plans'])->name('pricing');
Route::get('/our-story', function () {
    return view('theme::our-story');
})->name('our-story');
Route::get('/company', function () {
    return view('theme::company');
})->name('company');
Route::get('/our-team', function () {
    return view('theme::our-team');
})->name('our-team');
Route::get('/work-with-us', function () {
    return view('theme::work-with-us');
})->name('work-with-us');

// Job application routes - temporarily outside enforce.kyc middleware for testing
Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/marketplace/jobs/{job}/apply', [JobController::class, 'apply'])->name('marketplace.jobs.apply');
    Route::get('/marketplace/jobs/{job}/applications', [JobController::class, 'applications'])->name('marketplace.jobs.applications');
    Route::patch('/marketplace/jobs/{job}/applications/{application}', [JobController::class, 'updateApplicationStatus'])->name('marketplace.jobs.applications.update');
});

// Marketplace authenticated routes - require subscription and email verification
Route::middleware(['auth', 'verified', 'enforce.kyc'])->group(function () {
    Route::get('/marketplace/dashboard', [MarketplaceController::class, 'dashboard'])->name('marketplace.dashboard');
    Route::get('/marketplace/my-jobs', [MarketplaceController::class, 'myJobs'])->name('marketplace.my-jobs');
    Route::get('/marketplace/my-applications', [MarketplaceController::class, 'myApplications'])->name('marketplace.my-applications');
    Route::delete('/marketplace/applications/{application}', [MarketplaceController::class, 'withdrawApplication'])->name('marketplace.applications.withdraw');
    Route::get('/marketplace/messages', [MarketplaceController::class, 'messages'])->name('marketplace.messages');
    Route::get('/marketplace/messages/create/{user?}', [MarketplaceController::class, 'createMessage'])->name('marketplace.messages.create');
    Route::post('/marketplace/messages', [MarketplaceController::class, 'storeMessage'])->name('marketplace.messages.store');
    Route::get('/marketplace/messages/{conversation}', [MarketplaceController::class, 'showConversation'])->name('marketplace.messages.show');
    Route::get('/marketplace/jobs/{job}/edit', [JobController::class, 'edit'])->name('marketplace.jobs.edit')->middleware(['can.post.jobs', 'kyc.verified']);
    Route::put('/marketplace/jobs/{job}', [JobController::class, 'update'])->name('marketplace.jobs.update')->middleware(['can.post.jobs', 'kyc.verified']);
    Route::delete('/marketplace/jobs/{job}', [JobController::class, 'destroy'])->name('marketplace.jobs.destroy')->middleware(['can.post.jobs', 'kyc.verified']);
    Route::patch('/marketplace/jobs/{job}/promote', [JobController::class, 'promote'])->name('marketplace.jobs.promote')->middleware(['can.post.jobs', 'kyc.verified']);
    Route::get('/marketplace/jobs/create', [JobController::class, 'create'])->name('marketplace.jobs.create')->middleware(['can.post.jobs', 'kyc.verified']);
    Route::get('/marketplace/jobs/create-test', function() {
        return 'Test route works! User: ' . auth()->user()->name;
    })->name('marketplace.jobs.create-test')->middleware('can.post.jobs');
    Route::post('/marketplace/jobs', [JobController::class, 'store'])->name('marketplace.jobs.store')->middleware(['can.post.jobs', 'kyc.verified']);
});

// Authenticated routes - require email verification
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Job routes - require subscription for most actions
    Route::get('/jobs', [JobController::class, 'index'])->name('jobs.index')->middleware('subscription.required');
    Route::get('/jobs/create', [JobController::class, 'create'])->name('jobs.create')->middleware(['subscription.required', 'kyc.verified', 'subscription.limits:job_post']);
    Route::post('/jobs', [JobController::class, 'store'])->name('jobs.store')->middleware(['subscription.required', 'kyc.verified', 'subscription.limits:job_post']);
    Route::get('/jobs/applications', [JobController::class, 'userApplications'])->name('jobs.user-applications')->middleware('subscription.required');
    Route::get('/jobs/{job}/edit', [JobController::class, 'edit'])->name('jobs.edit')->middleware(['subscription.required', 'kyc.verified']);
    Route::put('/jobs/{job}', [JobController::class, 'update'])->name('jobs.update')->middleware(['subscription.required', 'kyc.verified']);
    Route::delete('/jobs/{job}', [JobController::class, 'destroy'])->name('jobs.destroy')->middleware(['subscription.required', 'kyc.verified']);
    
    // Job application routes - require subscription and KYC verification
    Route::post('/jobs/{job}/apply', [JobController::class, 'apply'])->name('jobs.apply')->middleware(['subscription.required', 'kyc.verified', 'subscription.limits:job_application']);
    Route::get('/jobs/{job}/applications', [JobController::class, 'applications'])->name('jobs.applications')->middleware(['subscription.required', 'kyc.verified']);
    Route::patch('/jobs/{job}/applications/{application}', [JobController::class, 'updateApplicationStatus'])->name('jobs.applications.update')->middleware(['subscription.required', 'kyc.verified']);
    Route::get('/jobs/{job}', [JobController::class, 'show'])->name('jobs.show')->middleware('subscription.required');
    
    // Messaging routes - use enforce.kyc like marketplace
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.web.index')->middleware('enforce.kyc');
    Route::get('/messages/create/{user?}', [MessageController::class, 'create'])->name('messages.create')->middleware('enforce.kyc');
    Route::get('/messages/{user}', [MessageController::class, 'show'])->name('messages.web.show')->middleware('enforce.kyc');
    Route::post('/messages/{user}', [MessageController::class, 'store'])->name('messages.web.store')->middleware('enforce.kyc');
    Route::post('/messages/{message}/mark-read', [MessageController::class, 'markAsRead'])->name('messages.web.mark-read')->middleware('enforce.kyc');

    // KYC routes
    Route::get('/kyc', [KycController::class, 'index'])->name('kyc.index');
    Route::get('/kyc/create', [KycController::class, 'create'])->name('kyc.create');
    Route::post('/kyc', [KycController::class, 'store'])->name('kyc.store');
    Route::get('/kyc/{kyc}', [KycController::class, 'show'])->name('kyc.show');
    Route::get('/kyc/{id}/download/{type}', [KycController::class, 'downloadFile'])->name('kyc.download');

    // Rating routes
    Route::get('/ratings', [RatingController::class, 'index'])->name('ratings.index');
    Route::get('/ratings/create', [RatingController::class, 'create'])->name('ratings.create');
    Route::post('/ratings', [RatingController::class, 'store'])->name('ratings.store');
    Route::get('/ratings/{rating}', [RatingController::class, 'show'])->name('ratings.show');
    Route::get('/ratings/{rating}/edit', [RatingController::class, 'edit'])->name('ratings.edit');
    Route::put('/ratings/{rating}', [RatingController::class, 'update'])->name('ratings.update');
    Route::delete('/ratings/{rating}', [RatingController::class, 'destroy'])->name('ratings.destroy');
    Route::get('/api/messages/unread-count', [MessageController::class, 'getUnreadCount'])->name('messages.unread-count');
    Route::get('/api/users/{user}/status', [MessageController::class, 'getUserStatus'])->name('users.status');
    
    // WebRTC routes for video/audio calls
    Route::post('/api/webrtc/signal', [\App\Http\Controllers\WebRTCController::class, 'signal'])->name('webrtc.signal');
    Route::get('/api/webrtc/signals', [\App\Http\Controllers\WebRTCController::class, 'getSignals'])->name('webrtc.get-signals');
    Route::post('/api/webrtc/initiate-call', [\App\Http\Controllers\WebRTCController::class, 'initiateCall'])->name('webrtc.initiate-call');
    Route::get('/api/webrtc/incoming-calls', [\App\Http\Controllers\WebRTCController::class, 'checkIncomingCalls'])->name('webrtc.incoming-calls');
    Route::post('/api/webrtc/respond-call', [\App\Http\Controllers\WebRTCController::class, 'respondToCall'])->name('webrtc.respond-call');
    Route::post('/api/webrtc/end-call', [\App\Http\Controllers\WebRTCController::class, 'endCall'])->name('webrtc.end-call');
    
// Profile routes - require email verification for authenticated profile actions
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [UserProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [UserProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [UserProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/kyc', [UserProfileController::class, 'kyc'])->name('profile.kyc');
    Route::post('/profile/kyc', [UserProfileController::class, 'submitKyc'])->name('profile.kyc.submit');
    Route::get('/profile/typing-test', [UserProfileController::class, 'typingTest'])->name('profile.typing-test');
    Route::post('/profile/typing-test', [UserProfileController::class, 'submitTypingTest'])->name('profile.typing-test.submit');
    Route::get('/profile/earnings-verification', [UserProfileController::class, 'earningsVerification'])->name('profile.earnings-verification');
    Route::post('/profile/earnings-verification', [UserProfileController::class, 'submitEarningsVerification'])->name('profile.earnings-verification.submit');
});

// Public profile routes (no auth required)
Route::get('/u/{user:username}', [UserProfileController::class, 'publicProfile'])->name('profile.public');

// Profile feature routes - require email verification
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile/feature', [App\Http\Controllers\ProfileFeatureController::class, 'show'])->name('profile.feature');
    Route::post('/profile/feature', [App\Http\Controllers\ProfileFeatureController::class, 'process'])->name('profile.feature.process');
    Route::get('/profile/feature/status', [App\Http\Controllers\ProfileFeatureController::class, 'canFeature'])->name('profile.feature.status');
});
    // Agency management routes (for agencies only)
    Route::middleware('agency.only')->prefix('agency')->name('agency.')->group(function () {
        Route::get('/employees', [AgencyManagementController::class, 'myEmployees'])->name('employees.index');
        Route::post('/contracts/{contract}/terminate', [AgencyManagementController::class, 'terminateContract'])->name('contracts.terminate');
        Route::post('/shifts/{shift}/review', [AgencyManagementController::class, 'reviewShift'])->name('shifts.review');
    });
    
    // Notification routes
    Route::get('/notifications', function() { return view('theme::pages.notifications.index'); })->name('notifications.index');
    Route::get('/api/notifications/unread-count', [NotificationController::class, 'getUnreadCount'])->name('notifications.unread-count');
    Route::post('/api/notifications/mark-read/{id}', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::post('/api/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::get('/api/notifications/recent-activity', [NotificationController::class, 'getRecentActivity'])->name('notifications.recent-activity');
    
    // Search routes
    Route::get('/search', [SearchController::class, 'index'])->name('search.index');
    Route::get('/api/search/suggestions', [SearchController::class, 'suggestions'])->name('search.suggestions');
    Route::get('/api/search/global', [SearchController::class, 'globalSearch'])->name('search.global');
    Route::get('/api/search/filters', [SearchController::class, 'filters'])->name('search.filters');
    
    // Subscription routes
    Route::get('/subscription/plans', [SubscriptionController::class, 'plans'])->name('subscription.plans');
    Route::get('/subscription/dashboard', [SubscriptionController::class, 'dashboard'])->name('subscription.dashboard');
    Route::post('/subscription/subscribe', [SubscriptionController::class, 'subscribe'])->name('subscription.subscribe');
    Route::get('/subscription/payment/{plan}', [SubscriptionController::class, 'payment'])->name('subscription.payment');
    Route::post('/subscription/payment/success', [SubscriptionController::class, 'paymentSuccess'])->name('subscription.payment.success');
    Route::get('/subscription/payment/cancel', [SubscriptionController::class, 'paymentCancel'])->name('subscription.payment.cancel');
    Route::post('/subscription/cancel', [SubscriptionController::class, 'cancel'])->name('subscription.cancel');
    
    // Subscription upgrade/downgrade routes
    Route::get('/subscription/plan/preview', [SubscriptionController::class, 'planPreview'])->name('subscription.plan.preview');
    Route::post('/subscription/upgrade', [SubscriptionController::class, 'upgrade'])->name('subscription.upgrade');
    Route::post('/subscription/downgrade', [SubscriptionController::class, 'downgrade'])->name('subscription.downgrade');
    
    // Job payment routes
    Route::get('/job/payment', [JobPaymentController::class, 'show'])->name('job.payment');
    Route::post('/job/payment/process', [JobPaymentController::class, 'process'])->name('job.payment.process');
    Route::get('/job/payment/success', [JobPaymentController::class, 'success'])->name('job.payment.success');
    Route::get('/job/payment/failure', [JobPaymentController::class, 'failure'])->name('job.payment.failure');
    
    // Contract routes
    Route::get('/contracts', [\App\Http\Controllers\ContractController::class, 'index'])->name('contracts.index');
    Route::get('/contracts/create', [\App\Http\Controllers\ContractController::class, 'create'])->name('contracts.create');
    Route::post('/contracts', [\App\Http\Controllers\ContractController::class, 'store'])->name('contracts.store');
    Route::get('/contracts/{contract}', [\App\Http\Controllers\ContractController::class, 'show'])->name('contracts.show');
    Route::get('/contracts/{contract}/edit', [\App\Http\Controllers\ContractController::class, 'edit'])->name('contracts.edit');
    Route::put('/contracts/{contract}', [\App\Http\Controllers\ContractController::class, 'update'])->name('contracts.update');
    Route::delete('/contracts/{contract}', [\App\Http\Controllers\ContractController::class, 'destroy'])->name('contracts.destroy');
    Route::post('/contracts/{contract}/earnings', [\App\Http\Controllers\ContractController::class, 'addEarning'])->name('contracts.add-earning');
    Route::delete('/contracts/{contract}/earnings/{earningIndex}', [\App\Http\Controllers\ContractController::class, 'removeEarning'])->name('contracts.remove-earning');
    Route::post('/contracts/{contract}/terminate-and-review', [\App\Http\Controllers\ContractController::class, 'terminateAndReview'])->name('contracts.terminate-and-review');
    
    // Contract review routes
    Route::post('/contracts/{contract}/reviews', [\App\Http\Controllers\ContractController::class, 'storeReview'])->name('contracts.reviews.store');
    Route::get('/contracts/{contract}/reviews/{review}/edit', [\App\Http\Controllers\ContractController::class, 'editReview'])->name('contracts.reviews.edit');
    Route::put('/contracts/{contract}/reviews/{review}', [\App\Http\Controllers\ContractController::class, 'updateReview'])->name('contracts.reviews.update');
    Route::delete('/contracts/{contract}/reviews/{review}', [\App\Http\Controllers\ContractController::class, 'destroyReview'])->name('contracts.reviews.destroy');
    
    // Contract approval routes
    Route::get('/contracts/approvals', [ContractApprovalController::class, 'index'])->name('contracts.approvals.index');
    Route::get('/contracts/approvals/{contract}', [ContractApprovalController::class, 'show'])->name('contracts.approvals.show');
    Route::post('/contracts/approvals/{contract}/accept', [ContractApprovalController::class, 'accept'])->name('contracts.approvals.accept');
    Route::post('/contracts/approvals/{contract}/reject', [ContractApprovalController::class, 'reject'])->name('contracts.approvals.reject');
});

// Email verification routes
Route::middleware('auth')->group(function () {
    Route::get('/email/verify', [EmailVerificationController::class, 'notice'])
        ->name('verification.notice');
    
    Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
    
    Route::post('/email/verification-notification', [EmailVerificationController::class, 'resend'])
        ->middleware('throttle:6,1')
        ->name('verification.send');
});

// Custom Auth Routes (outside middleware)
Route::middleware('guest')->group(function () {
    Route::get('/custom/login', function() {
        return view('auth.login');
    })->name('custom.login');

    Route::post('/custom/login', [CustomAuthController::class, 'login'])->name('custom.login.post');

    Route::get('/custom/register', [CustomAuthController::class, 'showRegistrationForm'])->name('custom.register');
    Route::post('/custom/register', [CustomAuthController::class, 'register'])
        ->name('custom.register.post')
        ->middleware('prevent.duplicate.registration');
});

// Public profile routes
Route::get('/profile/{user}', [UserProfileController::class, 'publicProfile'])->name('profile.public');

// User status route (for real-time presence)
Route::get('/users/{user}/status', [UserController::class, 'status'])->name('users.status');

// Test route for easy public profile access
Route::get('/test-public-profile', function () {
    return redirect()->route('profile.public', ['user' => 5]);
})->name('test.public.profile');

// Job Application Test Page
Route::get('/test-job-applications', function () {
    return '
    <!DOCTYPE html>
    <html>
    <head>
        <title>Job Application Testing</title>
        <style>
            body { font-family: Arial; margin: 50px; background: #f8f9fa; }
            .container { background: white; padding: 30px; border-radius: 10px; max-width: 800px; margin: 0 auto; }
            .section { margin: 30px 0; padding: 20px; background: #f1f1f1; border-radius: 5px; }
            h1 { color: #2c3e50; }
            h2 { color: #34495e; }
            .btn { display: inline-block; background: #3498db; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 5px; }
            .btn:hover { background: #2980b9; }
            .btn.success { background: #27ae60; }
            .btn.danger { background: #e74c3c; }
            .status { padding: 15px; margin: 10px 0; border-radius: 5px; }
            .status.success { background: #d4edda; color: #155724; }
            .status.error { background: #f8d7da; color: #721c24; }
            .code { background: #2c3e50; color: #ecf0f1; padding: 15px; border-radius: 5px; font-family: monospace; white-space: pre-wrap; }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>🔧 Job Application Testing Dashboard</h1>
            <p>Use this page to test the job application functionality step by step.</p>
            
            <div class="section">
                <h2>Step 1: Login</h2>
                <p>First, you need to log in to test job applications:</p>
                <a href="/test-login" class="btn success">Auto Login as Test User</a>
                <a href="/login-max" class="btn success">Login as Max (User #5)</a>
                <a href="/login-test" class="btn success">Login as Test User (User #2)</a>
            </div>
            
            <div class="section">
                <h2>Step 2: Check Status</h2>
                <p>After logging in, check your application status:</p>
                <a href="/debug-job-status/1" class="btn">Check Status for Job #1</a>
                <a href="/debug-user" class="btn">Check User Info</a>
            </div>
            
            <div class="section">
                <h2>Step 3: Test Application</h2>
                <p>Try the actual job application:</p>
                <a href="/marketplace/jobs/1" class="btn">Go to Job #1</a>
                <a href="/marketplace/jobs" class="btn">Browse All Jobs</a>
            </div>
            
            <div class="section">
                <h2>Troubleshooting</h2>
                <div class="status">
                    <strong>Common Issues:</strong><br>
                    • Not logged in → Use login links above<br>
                    • KYC verification required → Check user type and verification status<br>
                    • Already applied → Each user can only apply once<br>
                    • Job is full → Max applications reached<br>
                    • Own job → Cannot apply to your own job posting
                </div>
            </div>
            
            <div class="section">
                <h2>AJAX Testing</h2>
                <p>Test the AJAX functionality directly:</p>
                <div class="code" id="ajax-test">
To test AJAX:
1. Login using buttons above
2. Open browser console (F12)
3. Go to a job page
4. Fill out the application form
5. Submit and watch console for debug output
                </div>
                <button onclick="testAjax()" class="btn danger">Test AJAX Request</button>
            </div>
        </div>
        
        <script>
        function testAjax() {
            fetch("/debug-job-status/1")
                .then(response => response.json())
                .then(data => {
                    document.getElementById("ajax-test").innerHTML = 
                        "AJAX Test Result:\n" + JSON.stringify(data, null, 2);
                })
                .catch(error => {
                    document.getElementById("ajax-test").innerHTML = 
                        "AJAX Error:\n" + error.toString();
                });
        }
        </script>
    </body>
    </html>
    ';
})->name('test.job.applications');

// Simple working route
Route::get('/simple', function () {
    return '
    <!DOCTYPE html>
    <html>
    <head>
        <title>WORKING!</title>
        <style>
            body { font-family: Arial; margin: 50px; background: #f0f0f0; }
            .container { background: white; padding: 30px; border-radius: 10px; }
            h1 { color: green; }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>🎉 IT WORKS!</h1>
            <p>Your Laravel application is running correctly.</p>
            <p><strong>Current Time:</strong> ' . date('Y-m-d H:i:s') . '</p>
            <p><a href="/test-login">Auto Login</a> | <a href="/marketplace">Marketplace</a></p>
        </div>
    </body>
    </html>
    ';
});

// API test route
Route::get('/api-test', function () {
    return view('api-test');
});

// AJAX test route
Route::post('/test-ajax', [JobController::class, 'testAjax'])->name('test.ajax');

// Test job posting route without middleware restrictions
Route::post('/test-job-post', [JobController::class, 'testJobPost'])->name('test.job.post')->middleware(['auth']);

// Diagnostic route to help debug issues
Route::get('/debug-job-status/{job?}', function($jobId = 1, Request $request) {
    try {
        $user = auth()->user();
        $job = \App\Models\JobPost::findOrFail($jobId);
        
        $diagnostics = [
            'authenticated' => auth()->check(),
            'user_id' => $user ? $user->id : null,
            'user_name' => $user ? $user->name : null,
            'job_id' => $job->id,
            'job_title' => $job->title,
            'job_user_id' => $job->user_id,
            'is_own_job' => $user ? ($user->id === $job->user_id) : false,
            'has_applied' => $user ? $job->applications()->where('user_id', $user->id)->exists() : false,
            'job_full' => $job->current_applications >= $job->max_applications,
            'requires_verification' => $user ? $user->requiresVerification() : null,
            'can_apply' => false
        ];
        
        // Check if user can apply
        if ($user && $user->id !== $job->user_id && !$diagnostics['has_applied'] && !$diagnostics['job_full'] && !$diagnostics['requires_verification']) {
            $diagnostics['can_apply'] = true;
        }
        
        return response()->json($diagnostics);
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Debug error: ' . $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }
});

// Debug job application route - no middleware restrictions
Route::post('/debug-job-apply/{job}', function($jobId, Request $request) {
    try {
        $user = auth()->user();
        $job = \App\Models\JobPost::findOrFail($jobId);
        
        // Create a simple application without validation
        $application = \App\Models\JobApplication::create([
            'job_post_id' => $jobId,
            'user_id' => $user->id,
            'cover_letter' => $request->input('cover_letter', 'Debug application'),
            'proposed_rate' => $request->input('proposed_rate', 25.00),
            'available_hours' => $request->input('available_hours', 40),
            'status' => 'pending',
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'DEBUG: Application submitted successfully!',
            'application_id' => $application->id
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => 'DEBUG ERROR: ' . $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }
})->middleware('auth');
