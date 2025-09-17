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

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AgencyManagementController;
use App\Http\Controllers\CustomAuthController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\JobPaymentController;
use App\Http\Controllers\KycController;
use App\Http\Controllers\MarketplaceController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\UserProfileController;
use Wave\Facades\Wave;

// Wave routes first
Wave::routes();

// Override the Wave login route to redirect to our custom login
Route::get('login', fn () => redirect()->route('custom.login'))->name('custom.login.redirect');

// Redirect home to marketplace
Route::get('/', fn () => redirect()->route('marketplace.index'))->name('home');

// Marketplace routes
Route::get('marketplace', [MarketplaceController::class, 'index'])->name('marketplace.index');
Route::get('marketplace/jobs', [MarketplaceController::class, 'jobs'])->name('marketplace.jobs');
Route::get('marketplace/profiles', [MarketplaceController::class, 'profiles'])->name('marketplace.profiles');
Route::get('marketplace/chatters', [MarketplaceController::class, 'chatters'])->name('marketplace.chatters');
Route::get('marketplace/agencies', [MarketplaceController::class, 'agencies'])->name('marketplace.agencies');

// Marketplace job creation routes (must be before parameterized routes)
Route::get('marketplace/jobs/create', [JobController::class, 'create'])->name('marketplace.jobs.create')->middleware(['auth']);

// Marketplace parameterized routes (must be after specific routes)
Route::get('marketplace/jobs/{job}', [MarketplaceController::class, 'jobShow'])->name('marketplace.jobs.show');
Route::get('marketplace/profiles/{user}', [MarketplaceController::class, 'profileShow'])->name('marketplace.profiles.show');

// Platform routes
Route::get('platform/analytics', [MarketplaceController::class, 'analytics'])->name('platform.analytics')->middleware('auth');

// Secret admin access route (only accessible via direct URL)
Route::get('system/admin-access', function () {
    if (auth()->guest() || ! auth()->user()->isAdmin()) {
        abort(404);
    }

    return redirect()->route('platform.analytics');
})->name('system.admin-access')->middleware('auth');

// Admin verification management routes
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function (): void {
    Route::get('dashboard', fn () => redirect()->route('platform.analytics'))->name('dashboard');

    // KYC verification management
    Route::get('kyc', [AdminController::class, 'kycVerifications'])->name('kyc.index');
    Route::get('kyc/{verification}', [AdminController::class, 'showKycVerification'])->name('kyc.show');
    Route::patch('kyc/{verification}/status', [AdminController::class, 'updateKycStatus'])->name('kyc.update-status');

    // Earnings verification management
    Route::get('earnings', [AdminController::class, 'earningsVerifications'])->name('earnings.index');
    Route::get('earnings/{verification}', [AdminController::class, 'showEarningsVerification'])->name('earnings.show');
    Route::patch('earnings/{verification}/status', [AdminController::class, 'updateEarningsStatus'])->name('earnings.update-status');
    Route::get('earnings/{verification}/download/{type}', [AdminController::class, 'downloadEarningsFile'])->name('earnings.download');
    Route::get('earnings/{verification}/preview/{type}', [AdminController::class, 'previewEarningsFile'])->name('earnings.preview');
});
Route::get('platform/tools', [MarketplaceController::class, 'tools'])->name('platform.tools');
Route::get('platform/automation', [MarketplaceController::class, 'automation'])->name('platform.automation');
Route::get('platform/integrations', [MarketplaceController::class, 'integrations'])->name('platform.integrations');
Route::get('platform/api', [MarketplaceController::class, 'api'])->name('platform.api');

// Static pages
Route::get('about', fn () => view('theme::about'))->name('about');

// Temporary test route for authentication bypass
Route::get('test-login', function () {
    $user = App\Models\User::query()->where('email', 'test@example.com')->first();

    if ($user) {
        Auth::login($user);

        return redirect()->route('marketplace.index')->with('success', 'Logged in as test user');
    }

    return 'Test user not found';
});

// Login as Max (user with contract reviews)
Route::get('login-max', function () {
    $user = App\Models\User::find(5);

    if ($user) {
        Auth::login($user);

        return redirect()->route('profile.show')->with('success', 'Logged in as Max');
    }

    return 'Max user not found';
});

// Login as Test User (user who has given reviews)
Route::get('login-test', function () {
    $user = App\Models\User::find(2);

    if ($user) {
        Auth::login($user);

        return redirect()->route('profile.show')->with('success', 'Logged in as Test User');
    }

    return 'Test User not found';
});

// Test public profile for Max
Route::get('public-max', function () {
    $user = App\Models\User::find(5);

    if ($user) {
        return redirect()->route('profile.public', $user);
    }

    return 'Max user not found';
});
Route::get('pricing', [SubscriptionController::class, 'plans'])->name('pricing');
Route::get('our-story', fn () => view('theme::our-story'))->name('our-story');
Route::get('company', fn () => view('theme::company'))->name('company');
Route::get('our-team', fn () => view('theme::our-team'))->name('our-team');
Route::get('work-with-us', fn () => view('theme::work-with-us'))->name('work-with-us');

// Marketplace authenticated routes - require subscription
Route::middleware(['auth'])->group(function (): void {
    Route::get('marketplace/messages', [MarketplaceController::class, 'messages'])->name('marketplace.messages');
    Route::get('marketplace/messages/create/{user?}', [MarketplaceController::class, 'createMessage'])->name('marketplace.messages.create');
    Route::post('marketplace/messages', [MarketplaceController::class, 'storeMessage'])->name('marketplace.messages.store');
    Route::get('marketplace/messages/{conversation}', [MarketplaceController::class, 'showConversation'])->name('marketplace.messages.show');

    // Job application routes - require KYC verification
    Route::post('marketplace/jobs/{job}/apply', [JobController::class, 'apply'])->name('marketplace.jobs.apply')->middleware('kyc.verified');
    Route::get('marketplace/jobs/{job}/applications', [JobController::class, 'applications'])->name('marketplace.jobs.applications')->middleware('kyc.verified');
    Route::get('marketplace/jobs/{job}/edit', [JobController::class, 'edit'])->name('marketplace.jobs.edit')->middleware('kyc.verified');
    Route::put('marketplace/jobs/{job}', [JobController::class, 'update'])->name('marketplace.jobs.update')->middleware('kyc.verified');
    Route::delete('marketplace/jobs/{job}', [JobController::class, 'destroy'])->name('marketplace.jobs.destroy')->middleware('kyc.verified');
    Route::get('marketplace/jobs/create-test', fn () => 'Test route works! User: '.auth()->user()->name)->name('marketplace.jobs.create-test');
    Route::post('marketplace/jobs', [JobController::class, 'store'])->name('marketplace.jobs.store')->middleware('kyc.verified');
});

// Authenticated routes
Route::middleware('auth')->group(function (): void {
    Route::get('dashboard', [MarketplaceController::class, 'dashboard'])->name('dashboard');

    // Job routes - require subscription for most actions
    Route::get('jobs', [JobController::class, 'index'])->name('jobs.index')->middleware('subscription.required');
    Route::get('jobs/create', [JobController::class, 'create'])->name('jobs.create')->middleware(['subscription.required', 'kyc.verified', 'subscription.limits:job_post']);
    Route::post('jobs', [JobController::class, 'store'])->name('jobs.store')->middleware(['subscription.required', 'kyc.verified', 'subscription.limits:job_post']);
    Route::get('jobs/applications', [JobController::class, 'userApplications'])->name('jobs.user-applications')->middleware('subscription.required');
    Route::get('jobs/{job}/edit', [JobController::class, 'edit'])->name('jobs.edit')->middleware(['subscription.required', 'kyc.verified']);
    Route::put('jobs/{job}', [JobController::class, 'update'])->name('jobs.update')->middleware(['subscription.required', 'kyc.verified']);
    Route::delete('jobs/{job}', [JobController::class, 'destroy'])->name('jobs.destroy')->middleware(['subscription.required', 'kyc.verified']);

    // Job application routes - require subscription and KYC verification
    Route::post('jobs/{job}/apply', [JobController::class, 'apply'])->name('jobs.apply')->middleware(['subscription.required', 'kyc.verified', 'subscription.limits:job_application']);
    Route::get('jobs/{job}/applications', [JobController::class, 'applications'])->name('jobs.applications')->middleware(['subscription.required', 'kyc.verified']);
    Route::patch('jobs/{job}/applications/{application}', [JobController::class, 'updateApplicationStatus'])->name('jobs.applications.update')->middleware(['subscription.required', 'kyc.verified']);
    Route::get('jobs/{job}', [JobController::class, 'show'])->name('jobs.show')->middleware('subscription.required');

    // Messaging routes - require subscription
    Route::get('messages', [MessageController::class, 'index'])->name('messages.web.index')->middleware('subscription.required');
    Route::get('messages/create/{user?}', [MessageController::class, 'create'])->name('messages.create')->middleware('subscription.required');
    Route::get('messages/{user}', [MessageController::class, 'show'])->name('messages.web.show')->middleware('subscription.required');
    Route::post('messages/{user}', [MessageController::class, 'store'])->name('messages.web.store')->middleware('subscription.required');
    Route::post('messages/{message}/mark-read', [MessageController::class, 'markAsRead'])->name('messages.web.mark-read')->middleware('subscription.required');

    // KYC routes
    Route::get('kyc', [KycController::class, 'index'])->name('kyc.index');
    Route::get('kyc/create', [KycController::class, 'create'])->name('kyc.create');
    Route::post('kyc', [KycController::class, 'store'])->name('kyc.store');
    Route::get('kyc/{kyc}', [KycController::class, 'show'])->name('kyc.show');
    Route::get('kyc/{id}/download/{type}', [KycController::class, 'downloadFile'])->name('kyc.download');

    // Rating routes
    Route::get('ratings', [RatingController::class, 'index'])->name('ratings.index');
    Route::get('ratings/create', [RatingController::class, 'create'])->name('ratings.create');
    Route::post('ratings', [RatingController::class, 'store'])->name('ratings.store');
    Route::get('ratings/{rating}', [RatingController::class, 'show'])->name('ratings.show');
    Route::get('ratings/{rating}/edit', [RatingController::class, 'edit'])->name('ratings.edit');
    Route::put('ratings/{rating}', [RatingController::class, 'update'])->name('ratings.update');
    Route::delete('ratings/{rating}', [RatingController::class, 'destroy'])->name('ratings.destroy');
    Route::get('api/messages/unread-count', [MessageController::class, 'getUnreadCount'])->name('messages.unread-count');

    // Profile routes
    Route::get('profile', [UserProfileController::class, 'show'])->name('profile.show');
    Route::get('profile/edit', [UserProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [UserProfileController::class, 'update'])->name('profile.update');
    Route::get('profile/kyc', [UserProfileController::class, 'kyc'])->name('profile.kyc');
    Route::post('profile/kyc', [UserProfileController::class, 'submitKyc'])->name('profile.kyc.submit');
    Route::get('profile/earnings-verification', [UserProfileController::class, 'earningsVerification'])->name('profile.earnings-verification');
    Route::post('profile/earnings-verification', [UserProfileController::class, 'submitEarningsVerification'])->name('profile.earnings-verification.submit');
    Route::get('profile/typing-test', [UserProfileController::class, 'typingTest'])->name('profile.typing-test');
    Route::post('profile/typing-test', [UserProfileController::class, 'submitTypingTest'])->name('profile.typing-test.submit');

    // Agency management routes (for agencies only)
    Route::middleware('agency.only')->prefix('agency')->name('agency.')->group(function (): void {
        Route::get('employees', [AgencyManagementController::class, 'myEmployees'])->name('employees.index');
        Route::post('contracts/{contract}/terminate', [AgencyManagementController::class, 'terminateContract'])->name('contracts.terminate');
        Route::post('shifts/{shift}/review', [AgencyManagementController::class, 'reviewShift'])->name('shifts.review');
    });

    // Notification routes
    Route::get('notifications', fn () => view('theme::pages.notifications.index'))->name('notifications.index');
    Route::get('api/notifications/unread-count', [NotificationController::class, 'getUnreadCount'])->name('notifications.unread-count');
    Route::post('api/notifications/mark-read/{id}', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::post('api/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::get('api/notifications/recent-activity', [NotificationController::class, 'getRecentActivity'])->name('notifications.recent-activity');

    // Search routes
    Route::get('search', [SearchController::class, 'index'])->name('search.index');
    Route::get('api/search/suggestions', [SearchController::class, 'suggestions'])->name('search.suggestions');
    Route::get('api/search/global', [SearchController::class, 'globalSearch'])->name('search.global');
    Route::get('api/search/filters', [SearchController::class, 'filters'])->name('search.filters');

    // Subscription routes
    Route::get('subscription/plans', [SubscriptionController::class, 'plans'])->name('subscription.plans');
    Route::get('subscription/dashboard', [SubscriptionController::class, 'dashboard'])->name('subscription.dashboard');
    Route::post('subscription/subscribe', [SubscriptionController::class, 'subscribe'])->name('subscription.subscribe');
    Route::get('subscription/payment/{plan}', [SubscriptionController::class, 'payment'])->name('subscription.payment');
    Route::post('subscription/payment/success', [SubscriptionController::class, 'paymentSuccess'])->name('subscription.payment.success');
    Route::get('subscription/payment/cancel', [SubscriptionController::class, 'paymentCancel'])->name('subscription.payment.cancel');
    Route::post('subscription/cancel', [SubscriptionController::class, 'cancel'])->name('subscription.cancel');

    // Subscription upgrade/downgrade routes
    Route::get('subscription/plan/preview', [SubscriptionController::class, 'planPreview'])->name('subscription.plan.preview');
    Route::post('subscription/upgrade', [SubscriptionController::class, 'upgrade'])->name('subscription.upgrade');
    Route::post('subscription/downgrade', [SubscriptionController::class, 'downgrade'])->name('subscription.downgrade');

    // Job payment routes
    Route::get('job/payment', [JobPaymentController::class, 'show'])->name('job.payment');
    Route::post('job/payment/process', [JobPaymentController::class, 'process'])->name('job.payment.process');
    Route::get('job/payment/success', [JobPaymentController::class, 'success'])->name('job.payment.success');
    Route::get('job/payment/failure', [JobPaymentController::class, 'failure'])->name('job.payment.failure');

    // Contract routes
    Route::get('contracts', [\App\Http\Controllers\ContractController::class, 'index'])->name('contracts.index');
    Route::get('contracts/create', [\App\Http\Controllers\ContractController::class, 'create'])->name('contracts.create');
    Route::post('contracts', [\App\Http\Controllers\ContractController::class, 'store'])->name('contracts.store');
    Route::get('contracts/{contract}', [\App\Http\Controllers\ContractController::class, 'show'])->name('contracts.show');
    Route::get('contracts/{contract}/edit', [\App\Http\Controllers\ContractController::class, 'edit'])->name('contracts.edit');
    Route::put('contracts/{contract}', [\App\Http\Controllers\ContractController::class, 'update'])->name('contracts.update');
    Route::delete('contracts/{contract}', [\App\Http\Controllers\ContractController::class, 'destroy'])->name('contracts.destroy');
    Route::post('contracts/{contract}/earnings', [\App\Http\Controllers\ContractController::class, 'addEarning'])->name('contracts.add-earning');
    Route::delete('contracts/{contract}/earnings/{earningIndex}', [\App\Http\Controllers\ContractController::class, 'removeEarning'])->name('contracts.remove-earning');
    Route::post('contracts/{contract}/terminate-and-review', [\App\Http\Controllers\ContractController::class, 'terminateAndReview'])->name('contracts.terminate-and-review');

    // Contract review routes
    Route::post('contracts/{contract}/reviews', [\App\Http\Controllers\ContractController::class, 'storeReview'])->name('contracts.reviews.store');
    Route::get('contracts/{contract}/reviews/{review}/edit', [\App\Http\Controllers\ContractController::class, 'editReview'])->name('contracts.reviews.edit');
    Route::put('contracts/{contract}/reviews/{review}', [\App\Http\Controllers\ContractController::class, 'updateReview'])->name('contracts.reviews.update');
    Route::delete('contracts/{contract}/reviews/{review}', [\App\Http\Controllers\ContractController::class, 'destroyReview'])->name('contracts.reviews.destroy');
});

// Custom Auth Routes (outside middleware)
Route::middleware('guest')->group(function (): void {
    Route::get('custom/login', fn () => view('auth.login'))->name('custom.login');

    Route::post('custom/login', [CustomAuthController::class, 'login'])->name('custom.login.post');

    Route::get('custom/register', [CustomAuthController::class, 'showRegistrationForm'])->name('custom.register');
    Route::post('custom/register', [CustomAuthController::class, 'register'])->name('custom.register.post');
});

// Public profile routes
Route::get('profile/{user}', [UserProfileController::class, 'publicProfile'])->name('profile.public');

// Test route for easy public profile access
Route::get('test-public-profile', fn () => redirect()->route('profile.public', ['user' => 5]))->name('test.public.profile');

// API test route
Route::get('api-test', fn () => view('api-test'));
