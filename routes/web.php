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
use App\Http\Controllers\UserTypeChangeController;
use App\Http\Controllers\Admin\UserTypeChangeRequestController;
use App\Http\Controllers\ReviewContestController;
use App\Http\Controllers\Admin\ReviewContestController as AdminReviewContestController;
use App\Http\Controllers\UserAvailabilityController;
use App\Http\Controllers\UserTimezoneAvailabilityController;
use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\TypingTestController;
use App\Http\Controllers\TrainingController;
use App\Http\Controllers\MailController;

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
Route::get('/marketplace/jobs', [MarketplaceController::class, 'jobs'])->name('marketplace.jobs.index');
Route::get('/marketplace/profiles', [MarketplaceController::class, 'profiles'])->name('marketplace.profiles');
Route::get('/marketplace/chatters', [MarketplaceController::class, 'chatters'])->name('marketplace.chatters');
Route::get('/marketplace/agencies', [MarketplaceController::class, 'agencies'])->name('marketplace.agencies');
Route::get('/marketplace/job-posting-restricted', [MarketplaceController::class, 'jobPostingRestricted'])->name('marketplace.job-posting.restricted');

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

// Developer-only test routes (local only)
if (app()->environment('local')) {
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
}


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
    
    // User subscription management
    Route::get('/users/{user}/subscription/edit', [AdminController::class, 'editUserSubscription'])->name('users.subscription.edit');
    Route::post('/users/{user}/subscription/update', [AdminController::class, 'updateUserSubscription'])->name('users.subscription.update');
    Route::post('/users/{user}/subscription/quick-action', [AdminController::class, 'quickSubscriptionAction'])->name('users.subscription.quick-action');
    
    // User type management
    Route::post('/users/{user}/user-type/update', [AdminController::class, 'updateUserType'])->name('users.user-type.update');
    
    // Stop impersonating
    Route::post('/stop-impersonating', [AdminController::class, 'stopImpersonating'])->name('stop-impersonating');
    
    // KYC verification management
    Route::get('/kyc', [AdminController::class, 'kycVerifications'])->name('kyc.index');
    Route::get('/kyc/{verification}', [AdminController::class, 'showKycVerification'])->name('kyc.show');
    Route::patch('/kyc/{verification}/status', [AdminController::class, 'updateKycStatus'])->name('kyc.update-status');
    Route::get('/kyc/{verification}/download/{type}', [AdminController::class, 'downloadKycFile'])->name('kyc.download');
    Route::get('/kyc/{verification}/preview/{type}', [AdminController::class, 'previewKycFile'])->name('kyc.preview');
    
    // KYC and Earnings Verification Management
Route::post('/users/{user}/kyc-update', [AdminController::class, 'createOrUpdateKycVerification'])->name('users.kyc.update');
Route::post('/users/{user}/earnings-update', [AdminController::class, 'createOrUpdateEarningsVerification'])->name('users.earnings.update');
Route::delete('/users/{user}/kyc-remove', [AdminController::class, 'removeKycVerification'])->name('users.kyc.remove');
Route::delete('/users/{user}/earnings-remove', [AdminController::class, 'removeEarningsVerification'])->name('users.earnings.remove');
    
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
    Route::get('/admin-messages', [AdminController::class, 'messages'])->name('admin.messages.index');
    Route::delete('/admin-messages/{message}', [AdminController::class, 'deleteMessage'])->name('admin.messages.delete');
    
    // User type change request management (admin only)
    Route::get('/user-type-change-requests', [UserTypeChangeRequestController::class, 'index'])->name('user-type-change-requests.index');
    Route::get('/user-type-change-requests/{userTypeChangeRequest}', [UserTypeChangeRequestController::class, 'show'])->name('user-type-change-requests.show');
    Route::post('/user-type-change-requests/{userTypeChangeRequest}/approve', [UserTypeChangeRequestController::class, 'approve'])->name('user-type-change-requests.approve');
    Route::post('/user-type-change-requests/{userTypeChangeRequest}/reject', [UserTypeChangeRequestController::class, 'reject'])->name('user-type-change-requests.reject');
    Route::post('/user-type-change-requests/bulk-approve', [UserTypeChangeRequestController::class, 'bulkApprove'])->name('user-type-change-requests.bulk-approve');
    Route::post('/user-type-change-requests/bulk-reject', [UserTypeChangeRequestController::class, 'bulkReject'])->name('user-type-change-requests.bulk-reject');
    
    // Review contest management (admin only)
    Route::get('/contests', [\App\Http\Controllers\Admin\ReviewContestController::class, 'index'])->name('contests.index');
    Route::get('/contests/{contest}', [\App\Http\Controllers\Admin\ReviewContestController::class, 'show'])->name('contests.show');
    Route::post('/contests/{contest}/approve', [\App\Http\Controllers\Admin\ReviewContestController::class, 'approve'])->name('contests.approve');
    Route::post('/contests/{contest}/reject', [\App\Http\Controllers\Admin\ReviewContestController::class, 'reject'])->name('contests.reject');
    Route::post('/contests/bulk-approve', [\App\Http\Controllers\Admin\ReviewContestController::class, 'bulkApprove'])->name('contests.bulk-approve');
    Route::post('/contests/bulk-reject', [\App\Http\Controllers\Admin\ReviewContestController::class, 'bulkReject'])->name('contests.bulk-reject');
    
    // API endpoints for admin dashboard
    Route::get('/api/stats', [AdminController::class, 'getStats'])->name('api.stats');
});
Route::get('/platform/tools', [MarketplaceController::class, 'tools'])->name('platform.tools');
Route::get('/platform/automation', [MarketplaceController::class, 'automation'])->name('platform.automation');
Route::get('/platform/integrations', [MarketplaceController::class, 'integrations'])->name('platform.integrations');
Route::get('/platform/api', [MarketplaceController::class, 'api'])->name('platform.api');

// Static pages
Route::get('/about', function () {
    return view('theme::pages.about');
})->name('about');

// Resource pages
Route::get('/resources/getting-started', function () {
    return view('theme::pages.resources.getting-started');
})->name('resources.getting-started');

Route::get('/resources/best-practices', function () {
    return view('theme::pages.resources.best-practices');
})->name('resources.best-practices');

Route::get('/resources/safety-guidelines', function () {
    return view('theme::pages.resources.safety-guidelines');
})->name('resources.safety-guidelines');

Route::get('/resources/faq', function () {
    return view('theme::pages.resources.faq');
})->name('resources.faq');

Route::get('/resources/video-tutorials', function () {
    return response()->view('theme::pages.resources.video-tutorials', [
        'comingSoon' => true
    ]);
})->name('resources.video-tutorials');

Route::get('/resources/support', function () {
    return view('theme::pages.resources.support');
})->name('resources.support');

Route::get('/terms-of-service', function () {
    return view('theme::pages.terms-of-service');
})->name('terms-of-service');

Route::get('/privacy-policy', function () {
    return view('theme::pages.privacy-policy');
})->name('privacy-policy');

Route::get('/trust-safety', function () {
    return view('theme::pages.trust-safety');
})->name('trust-safety');

Route::get('/contact', function () {
    return view('theme::pages.contact');
})->name('contact');

Route::get('/link-test', function () {
    return view('theme::pages.link-test');
})->name('link-test');

// Typing Test Routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/typing-tests', [TypingTestController::class, 'index'])->name('typing-tests.index');
    Route::get('/typing-tests/{language}', [TypingTestController::class, 'show'])->name('typing-tests.show');
    Route::post('/typing-tests/{language}/submit', [TypingTestController::class, 'submit'])->name('typing-tests.submit');
});

// Chatter Test Center (centralized dashboard for all tests)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/chatter/tests', [\App\Http\Controllers\ChatterTestController::class, 'index'])->name('chatter.tests');
    Route::get('/chatter/take-test', [\App\Http\Controllers\ChatterTestController::class, 'takeTest'])->name('chatter.take-test');
    Route::get('/chatter/results', [\App\Http\Controllers\ChatterTestController::class, 'results'])->name('chatter.results');
});

// CSRF Token Refresh Route
Route::get('/csrf-token', function () {
    return response()->json(['token' => csrf_token()]);
})->middleware('web');

// Training Routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/training', [TrainingController::class, 'index'])->name('training.index');
    Route::get('/training/module/{module}', [TrainingController::class, 'show'])->name('training.module');
    Route::post('/training/module/{module}/complete', [TrainingController::class, 'complete'])->name('training.complete');
    Route::get('/training/test/{test}', [TrainingController::class, 'showTest'])->name('training.test.show');
    Route::post('/training/test/{test}/submit', [TrainingController::class, 'submitTest'])->name('training.test.submit');
    Route::get('/training/progress', [TrainingController::class, 'progress'])->name('training.progress');
});

// Developer-only authentication and test routes (local only)
if (app()->environment('local')) {
    // Temporary test route for authentication bypass
    Route::get('/test-login', function () {
        $user = App\Models\User::where('email', 'test@example.com')->first();
        if ($user) {
            Auth::login($user);
            return redirect()->route('marketplace.index')->with('success', 'Logged in as test user');
        }
        return 'Test user not found';
    });

    // Login as admin user
    Route::get('/login-max', function () {
        $user = App\Models\User::first();
        if ($user) {
            Auth::login($user);
            return redirect()->route('ratings.contests.index')->with('success', 'Logged in as ' . $user->name);
        }
        return 'No users found';
    });

    // Test typing tests without middleware
    Route::get('/test-typing-tests', function () {
        $user = App\Models\User::first();
        if ($user) {
            Auth::login($user);
            try {
                $controller = new App\Http\Controllers\TypingTestController();
                return $controller->index();
            } catch (Exception $e) {
                return 'Error: ' . $e->getMessage() . '<br><br>Trace: <pre>' . $e->getTraceAsString() . '</pre>';
            }
        }
        return 'No users found';
    });

    Route::get('/test-typing-test-show/{language}', function ($language) {
        $user = App\Models\User::first();
        if ($user) {
            Auth::login($user);
            try {
                $controller = new App\Http\Controllers\TypingTestController();
                return $controller->show($language);
            } catch (Exception $e) {
                return 'Error: ' . $e->getMessage() . '<br><br>Trace: <pre>' . $e->getTraceAsString() . '</pre>';
            }
        }
        return 'No users found';
    });

    // Test direct access to contests
    Route::get('/test-contests', function () {
        if (!auth()->check()) {
            return 'Not logged in. <a href="/login-max">Login first</a>';
        }
        
        try {
            $controller = new App\Http\Controllers\ReviewContestController();
            return $controller->index();
        } catch (Exception $e) {
            return 'Error: ' . $e->getMessage() . '<br><br>Trace: <pre>' . $e->getTraceAsString() . '</pre>';
        }
    });

    // Test ReviewContest model directly
    Route::get('/test-review-contest-model', function () {
        if (!auth()->check()) {
            return 'Not logged in. <a href="/login-max">Login first</a>';
        }
        
        try {
            // Test basic model access
            $contests = App\Models\ReviewContest::where('contested_by', auth()->id())->get();
            return 'Found ' . $contests->count() . ' contests for user ' . auth()->id();
        } catch (Exception $e) {
            return 'Model Error: ' . $e->getMessage() . '<br><br>Trace: <pre>' . $e->getTraceAsString() . '</pre>';
        }
    });

    // Test training buttons debug
    Route::get('/test-training-debug', function () {
        $user = App\Models\User::first();
        if ($user) {
            Auth::login($user);
            try {
                $controller = new App\Http\Controllers\TrainingController();
                $response = $controller->index();
                return $response;
            } catch (Exception $e) {
                return 'Training Error: ' . $e->getMessage() . '<br><br>File: ' . $e->getFile() . '<br>Line: ' . $e->getLine() . '<br><br>Trace: <pre>' . $e->getTraceAsString() . '</pre>';
            }
        }
        return 'No users found';
    });

    // Test chatter tests without auth
    Route::get('/test-chatter-tests', function () {
        $user = App\Models\User::first();
        if ($user) {
            Auth::login($user);
            try {
                $controller = new App\Http\Controllers\ChatterTestController();
                return $controller->index();
            } catch (Exception $e) {
                return 'Controller Error: ' . $e->getMessage() . '<br><br>File: ' . $e->getFile() . '<br>Line: ' . $e->getLine() . '<br><br>Trace: <pre>' . $e->getTraceAsString() . '</pre>';
            }
        }
        return 'No users found';
    });
}

// Mail (email-like) routes
Route::middleware(['auth'])
    ->prefix('mail')
    ->name('mail.')
    ->group(function () {
        Route::get('/', [MailController::class, 'index'])->name('index');
        Route::get('/sent', [MailController::class, 'sent'])->name('sent');
        Route::get('/compose', [MailController::class, 'compose'])->name('compose');
        Route::post('/send', [MailController::class, 'send'])->name('send');
        Route::get('/conversations/{conversation}', [MailController::class, 'show'])->name('show');
    });

// Test ReviewContest with relations
Route::get('/test-review-contest-relations', function () {
    if (!auth()->check()) {
        return 'Not logged in. <a href="/login-max">Login first</a>';
    }
    
    try {
        // Test with relations like in the controller
        $contests = App\Models\ReviewContest::with(['rating', 'reviewedBy'])
            ->where('contested_by', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();
        return 'Found ' . $contests->count() . ' contests with relations for user ' . auth()->id();
    } catch (Exception $e) {
        return 'Relations Error: ' . $e->getMessage() . '<br><br>Trace: <pre>' . $e->getTraceAsString() . '</pre>';
    }
});

// Test contests route without any middleware
Route::get('/contests-direct', function () {
    if (!auth()->check()) {
        return 'Not logged in. <a href="/login-max">Login first</a>';
    }
    
    try {
        // Simulate the exact controller logic
        $contests = App\Models\ReviewContest::with(['rating', 'reviewedBy'])
            ->where('contested_by', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('theme::ratings.contests.index', compact('contests'));
    } catch (Exception $e) {
        return 'Direct Route Error: ' . $e->getMessage() . '<br><br>Trace: <pre>' . $e->getTraceAsString() . '</pre>';
    }
});

// Simple contests route with basic HTML output
Route::get('/contests-simple', function () {
    if (!auth()->check()) {
        return 'Not logged in. <a href="/login-max">Login first</a>';
    }
    
    try {
        $contests = App\Models\ReviewContest::with(['rating', 'reviewedBy'])
            ->where('contested_by', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

        $html = '<h1>My Review Contests</h1>';
        $html .= '<p>Found ' . $contests->count() . ' contests for user ' . auth()->id() . '</p>';
        
        if ($contests->count() > 0) {
            $html .= '<ul>';
            foreach ($contests as $contest) {
                $html .= '<li>';
                $html .= 'Contest ID: ' . $contest->id . ' | ';
                $html .= 'Status: ' . $contest->status . ' | ';
                $html .= 'Created: ' . $contest->created_at->format('Y-m-d H:i:s');
                if ($contest->rating) {
                    $html .= ' | Rating by: ' . ($contest->rating->rater ? $contest->rating->rater->name : 'Unknown');
                }
                $html .= '</li>';
            }
            $html .= '</ul>';
        } else {
            $html .= '<p>No contests found.</p>';
        }
        
        $html .= '<br><a href="/ratings">← Back to Ratings</a>';
        
        return $html;
    } catch (Exception $e) {
        return 'Simple Route Error: ' . $e->getMessage() . '<br><br>Trace: <pre>' . $e->getTraceAsString() . '</pre>';
    }
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

// Debug route for training access
Route::get('/training-debug', function () {
    // Find or create a chatter user
    $chatterType = App\Models\UserType::firstOrCreate(['name' => 'chatter']);
    $user = App\Models\User::firstOrCreate(
        ['email' => 'chatter@test.com'],
        [
            'name' => 'Test Chatter',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'user_type_id' => $chatterType->id
        ]
    );
    
    Auth::login($user);
    
    // Get training data like the controller does
    $modules = App\Models\TrainingModule::active()->ordered()->withCount('tests')->get();
    $userProgress = App\Models\UserTrainingProgress::where('user_id', $user->id)->get()->keyBy('training_module_id');
    $totalModulesCount = $modules->count();
    $completedModulesCount = $userProgress->where('status', 'completed')->count();
    
    $output = '<h1>Training Debug Info</h1>';
    $output .= '<p>Logged in as: ' . $user->name . ' (' . $user->email . ')</p>';
    $output .= '<p>User Type: ' . ($user->userType ? $user->userType->name : 'None') . '</p>';
    $output .= '<p>Email Verified: ' . ($user->hasVerifiedEmail() ? 'Yes' : 'No') . '</p>';
    $output .= '<p>Total Modules: ' . $totalModulesCount . '</p>';
    $output .= '<p>Completed Modules: ' . $completedModulesCount . '</p>';
    $output .= '<p>User Progress Records: ' . $userProgress->count() . '</p>';
    $output .= '<h2>Available Modules:</h2><ul>';
    
    foreach ($modules as $module) {
        $progress = $userProgress->get($module->id);
        $status = $progress ? $progress->status : 'not_started';
        $output .= '<li>' . $module->title . ' - Status: ' . $status . ' - Tests: ' . $module->tests_count . '</li>';
    }
    $output .= '</ul>';
    $output .= '<p><a href="/training">Go to Training Page</a></p>';
    
    return $output;
});

// Login as chatter for easy testing
Route::get('/login-chatter', function () {
    $chatterType = App\Models\UserType::firstOrCreate(['name' => 'chatter']);
    $user = App\Models\User::firstOrCreate(
        ['email' => 'chatter@test.com'],
        [
            'name' => 'Test Chatter',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'user_type_id' => $chatterType->id
        ]
    );
    
    Auth::login($user);
    return redirect('/training-debug')->with('success', 'Logged in as chatter');
});

// Simple training display - working version
Route::get('/training-simple', function () {
    try {
        // Auto login as chatter
        $chatterType = App\Models\UserType::firstOrCreate(['name' => 'chatter']);
        $user = App\Models\User::firstOrCreate(
            ['email' => 'chatter@test.com'],
            [
                'name' => 'Test Chatter',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'user_type_id' => $chatterType->id
            ]
        );
        Auth::login($user);
        
        // Get basic training data
        $modules = App\Models\TrainingModule::where('is_active', true)->orderBy('order')->get();
        $userProgress = App\Models\UserTrainingProgress::where('user_id', $user->id)->get();
        
        $html = '<!DOCTYPE html><html><head><title>Training Modules</title><style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }
        .container { max-width: 1000px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .module { background: #f8f9fa; padding: 20px; margin: 20px 0; border-radius: 8px; border-left: 4px solid #007bff; }
        .module h3 { color: #333; margin-top: 0; }
        .module p { color: #666; line-height: 1.6; }
        .meta { font-size: 14px; color: #888; margin: 10px 0; }
        .btn { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn:hover { background: #0056b3; }
        .status { padding: 5px 10px; border-radius: 15px; font-size: 12px; font-weight: bold; }
        .status.completed { background: #d4edda; color: #155724; }
        .status.in-progress { background: #fff3cd; color: #856404; }
        .status.not-started { background: #f8d7da; color: #721c24; }
        </style></head><body>';
        
        $html .= '<div class="container">';
        $html .= '<h1>🎓 Training Modules</h1>';
        $html .= '<p>Welcome, <strong>' . $user->name . '</strong>! Complete all training modules to unlock your profile visibility.</p>';
        $html .= '<hr>';
        
        foreach ($modules as $module) {
            $progress = $userProgress->where('training_module_id', $module->id)->first();
            $status = $progress ? $progress->status : 'not_started';
            $statusClass = str_replace('_', '-', $status);
            
            $html .= '<div class="module">';
            $html .= '<div style="display: flex; justify-content: space-between; align-items: start;">';
            $html .= '<div><h3>' . $module->title . '</h3></div>';
            $html .= '<span class="status ' . $statusClass . '">' . ucfirst(str_replace('_', ' ', $status)) . '</span>';
            $html .= '</div>';
            $html .= '<p>' . $module->description . '</p>';
            $html .= '<div class="meta">';
            if ($module->duration_minutes) {
                $html .= '⏱️ ' . $module->duration_minutes . ' minutes • ';
            }
            $testsCount = App\Models\TrainingTest::where('training_module_id', $module->id)->count();
            $html .= '📝 ' . $testsCount . ' test' . ($testsCount != 1 ? 's' : '') . ' available';
            $html .= '</div>';
            $html .= '<a href="/training/module/' . $module->id . '" class="btn">View Module</a>';
            $html .= '</div>';
        }
        
        $html .= '<div style="margin-top: 30px; padding: 20px; background: #e9ecef; border-radius: 8px;">';
        $html .= '<h3>📊 Your Progress</h3>';
        $completed = $userProgress->where('status', 'completed')->count();
        $total = $modules->count();
        $html .= '<p>Completed: <strong>' . $completed . '/' . $total . '</strong> modules</p>';
        $percentage = $total > 0 ? round(($completed / $total) * 100) : 0;
        $html .= '<div style="background: #dee2e6; height: 20px; border-radius: 10px; overflow: hidden;">';
        $html .= '<div style="background: #28a745; height: 100%; width: ' . $percentage . '%; transition: width 0.3s;"></div>';
        $html .= '</div>';
        $html .= '<p style="margin-top: 10px; font-size: 14px; color: #666;">Progress: ' . $percentage . '%</p>';
        $html .= '</div>';
        
        $html .= '<div style="margin-top: 20px; text-align: center;">';
        $html .= '<a href="/training" class="btn" style="background: #6c757d;">Go to Official Training Page</a>';
        $html .= '</div>';
        
        $html .= '</div></body></html>';
        
        return $html;
        
    } catch (Exception $e) {
        return '<h1>Error</h1><p>' . $e->getMessage() . '</p><pre>' . $e->getTraceAsString() . '</pre>';
    }
});

// Developer-only training test routes (local only)
if (app()->environment('local')) {
    // Training buttons test route
    Route::get('/training-buttons-test', function () {
        try {
            // Find or create a chatter user and login
            $chatterType = App\Models\UserType::firstOrCreate(['name' => 'chatter']);
            $user = App\Models\User::firstOrCreate(
                ['email' => 'chatter@test.com'],
                [
                    'name' => 'Test Chatter',
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                    'user_type_id' => $chatterType->id
                ]
            );
            
            Auth::login($user);
            
            // Get training data
            $modules = App\Models\TrainingModule::active()->ordered()->withCount('tests')->get();
            
            // Create a simple HTML page with buttons
            $html = '<!DOCTYPE html><html><head><title>Training Buttons Test</title>';
            $html .= '<meta name="csrf-token" content="' . csrf_token() . '">';
            $html .= '<style>'
                . 'body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }'
                . '.container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; }'
                . '.module { background: #f8f9fa; padding: 20px; margin: 20px 0; border-radius: 8px; }'
                . '.btn { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; margin: 10px 5px; }'
                . '.btn:hover { background: #0056b3; }'
                . '.btn-success { background: #28a745; }'
                . '.btn-warning { background: #ffc107; color: #212529; }'
                . '.btn-secondary { background: #6c757d; }'
                . '#result { margin-top: 20px; padding: 15px; background: #e9ecef; border-radius: 5px; }'
                . '</style></head><body>';
            
            $html .= '<div class="container">';
            $html .= '<h1>🧪 Training Buttons Test</h1>';
            $html .= '<p>Logged in as: <strong>' . $user->name . '</strong> (' . $user->email . ')</p>';
            $html .= '<p>Total modules: ' . $modules->count() . '</p>';
            $html .= '<hr>';
            
            foreach ($modules as $module) {
                $html .= '<div class="module">';
                $html .= '<h3>' . $module->title . '</h3>';
                $html .= '<p>' . $module->description . '</p>';
                $html .= '<div>';
                $html .= '<a href="/training/module/' . $module->id . '" class="btn">View Module (Direct Link)</a>';
                $html .= '<button onclick="testModuleButton(' . $module->id . ')" class="btn btn-success">Test Button</button>';
                $html .= '<button onclick="testCompleteModule(' . $module->id . ')" class="btn btn-warning">Test Complete</button>';
                $html .= '</div>';
                $html .= '</div>';
            }
            
            $html .= '<div id="result"></div>';
            $html .= '<hr>';
            $html .= '<p><a href="/training" class="btn btn-secondary">Go to Real Training Page</a></p>';
            
            $html .= '<script>'
                . 'function testModuleButton(moduleId) {'
                    . 'document.getElementById("result").innerHTML = "Testing module " + moduleId + "...";'
                    . 'window.location.href = "/training/module/" + moduleId;'
                . '}'
                . 'function testCompleteModule(moduleId) {'
                    . 'const csrfToken = document.querySelector("meta[name=\"csrf-token\"]").getAttribute("content");'
                    . 'document.getElementById("result").innerHTML = "Testing complete for module " + moduleId + "...";'
                    . 'fetch("/training/module/" + moduleId + "/complete", {'
                        . 'method: "POST",'
                        . 'headers: {'
                            . '"Content-Type": "application/json",'
                            . '"X-CSRF-TOKEN": csrfToken'
                        . '}'
                    . '})'
                    . '.then(response => response.json())'
                    . '.then(data => {'
                        . 'document.getElementById("result").innerHTML = "Result: " + JSON.stringify(data, null, 2);'
                    . '})'
                    . '.catch(error => {'
                        . 'document.getElementById("result").innerHTML = "Error: " + error.toString();'
                    . '});'
                . '}'
                . '</script>';
            
            $html .= '</div></body></html>';
            
            return $html;
            
        } catch (Exception $e) {
            return 'Training Buttons Test Error: ' . $e->getMessage() . '<br><br>Trace: <pre>' . $e->getTraceAsString() . '</pre>';
        }
    });

    // Training bypass route - no middleware
    Route::get('/training-bypass', function () {
        try {
            // Find or create a chatter user and login
            $chatterType = App\Models\UserType::firstOrCreate(['name' => 'chatter']);
            $user = App\Models\User::firstOrCreate(
                ['email' => 'chatter@test.com'],
                [
                    'name' => 'Test Chatter',
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                    'user_type_id' => $chatterType->id
                ]
            );
            
            Auth::login($user);
            
            // Manually execute controller logic
            $modules = App\Models\TrainingModule::active()->ordered()->withCount('tests')->get();
            $userProgress = App\Models\UserTrainingProgress::where('user_id', $user->id)->get()->keyBy('training_module_id');
            $totalModulesCount = $modules->count();
            $completedModulesCount = $userProgress->where('status', 'completed')->count();
            
            // Simple typing test check
            $hasPassedTypingTest = App\Models\UserTestResult::where('user_id', $user->id)
                ->where('testable_type', 'App\\Models\\TypingTest')
                ->where('passed', true)
                ->exists();
            
            $requirements = [
                'email_verified' => $user->hasVerifiedEmail(),
                'kyc_completed' => method_exists($user, 'isKycVerified') ? $user->isKycVerified() : false,
                'training_completed' => $completedModulesCount === $totalModulesCount && $totalModulesCount > 0,
                'typing_test_passed' => $hasPassedTypingTest
            ];
            
            $allRequirementsMet = array_reduce($requirements, function($carry, $requirement) {
                return $carry && $requirement;
            }, true);
            
            // Return training view manually
            return view('training.index', compact(
                'modules', 
                'userProgress', 
                'totalModulesCount', 
                'completedModulesCount',
                'requirements',
                'allRequirementsMet'
            ));
            
        } catch (Exception $e) {
            return 'Training Bypass Error: ' . $e->getMessage() . '<br><br>Trace: <pre>' . $e->getTraceAsString() . '</pre>';
        }
    });
}

// Developer-only: Debug profile route
if (app()->environment('local')) {
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
}

// Developer-only
if (app()->environment('local')) {
Route::get('/public-max', function () {
    $user = App\Models\User::find(5);
    if ($user) {
        return redirect()->route('profile.public', $user);
    }
    return 'Max user not found';
});
}
Route::get('/pricing', [SubscriptionController::class, 'plans'])->name('pricing');

// Stripe webhook route (no CSRF protection needed)
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle'])
    ->name('stripe.webhook')
    ->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

// Subscription routes - public pricing page is above
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/subscription/dashboard', [SubscriptionController::class, 'dashboard'])->name('subscription.dashboard');
    Route::post('/subscription/subscribe', [SubscriptionController::class, 'subscribe'])->name('subscription.subscribe');
    Route::get('/subscription/plan/preview', [SubscriptionController::class, 'planPreview'])->name('subscription.plan.preview');
    Route::post('/subscription/upgrade', [SubscriptionController::class, 'upgrade'])->name('subscription.upgrade');
    Route::post('/subscription/downgrade', [SubscriptionController::class, 'downgrade'])->name('subscription.downgrade');
    Route::get('/subscription/payment/{plan}', [SubscriptionController::class, 'payment'])->name('subscription.payment');
    Route::get('/subscription/payment/cancel', [SubscriptionController::class, 'paymentCancel'])->name('subscription.payment.cancel');
    Route::get('/subscription/payment/success', [SubscriptionController::class, 'paymentSuccess'])->name('subscription.payment.success');
    Route::post('/subscription/cancel', [SubscriptionController::class, 'cancel'])->name('subscription.cancel');
});
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

// Job application routes - fixed without middleware restrictions
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
    // Messaging feature removed for redevelopment
    Route::get('/marketplace/jobs/{job}/edit', [JobController::class, 'edit'])->name('marketplace.jobs.edit')->middleware(['can.post.jobs', 'kyc.verified']);
    Route::put('/marketplace/jobs/{job}', [JobController::class, 'update'])->name('marketplace.jobs.update')->middleware(['can.post.jobs', 'kyc.verified']);
    Route::delete('/marketplace/jobs/{job}', [JobController::class, 'destroy'])->name('marketplace.jobs.destroy')->middleware(['can.post.jobs', 'kyc.verified']);
    Route::delete('/marketplace/jobs/{job}/delete', [JobController::class, 'destroy'])->name('marketplace.jobs.delete')->middleware(['can.post.jobs', 'kyc.verified']);
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
    
    // Job-posts route aliases for consistency (updated route names to avoid conflicts)
    Route::get('/job-posts', [JobController::class, 'index'])->name('job-posts')->middleware('subscription.required');
    Route::get('/job-posts/create', [JobController::class, 'create'])->name('job-posts.create-alias')->middleware(['subscription.required', 'kyc.verified', 'subscription.limits:job_post']);
    Route::post('/job-posts', [JobController::class, 'store'])->name('job-posts.store-alias')->middleware(['subscription.required', 'kyc.verified', 'subscription.limits:job_post']);
    Route::get('/job-posts/{job}/edit', [JobController::class, 'edit'])->name('job-posts.edit-alias')->middleware(['subscription.required', 'kyc.verified']);
    Route::put('/job-posts/{job}', [JobController::class, 'update'])->name('job-posts.update-alias')->middleware(['subscription.required', 'kyc.verified']);
    Route::delete('/job-posts/{job}', [JobController::class, 'destroy'])->name('job-posts.destroy-alias')->middleware(['subscription.required', 'kyc.verified']);
    Route::get('/job-posts/{job}', [JobController::class, 'show'])->name('job-posts.show-alias')->middleware('subscription.required');
    Route::get('/jobs/applications', [JobController::class, 'userApplications'])->name('jobs.user-applications')->middleware('subscription.required');
    Route::get('/jobs/{job}/edit', [JobController::class, 'edit'])->name('jobs.edit')->middleware(['subscription.required', 'kyc.verified']);
    Route::put('/jobs/{job}', [JobController::class, 'update'])->name('jobs.update')->middleware(['subscription.required', 'kyc.verified']);
    Route::delete('/jobs/{job}', [JobController::class, 'destroy'])->name('jobs.destroy')->middleware(['subscription.required', 'kyc.verified']);
    
    // Job application routes - require subscription and KYC verification
    Route::post('/jobs/{job}/apply', [JobController::class, 'apply'])->name('jobs.apply')->middleware(['subscription.required', 'kyc.verified', 'subscription.limits:job_application']);
    Route::get('/jobs/{job}/applications', [JobController::class, 'applications'])->name('jobs.applications')->middleware(['subscription.required', 'kyc.verified']);
    Route::patch('/jobs/{job}/applications/{application}', [JobController::class, 'updateApplicationStatus'])->name('jobs.applications.update')->middleware(['subscription.required', 'kyc.verified']);
    Route::get('/jobs/{job}', [JobController::class, 'show'])->name('jobs.show')->middleware('subscription.required');
    
});

// Primary Real-time Messaging System
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/messages', function () {
        return view('messages.realtime');
    })->name('messages');
    Route::any('/messages/{any?}', function () { 
        return redirect()->route('messages'); 
    })->where('any', '.*');
});

// Messaging System Routes - Consolidated and Fixed
Route::middleware(['auth', 'verified'])->prefix('messages')->name('messages.')->group(function () {
    // Main messaging interface - now uses real-time WebSocket interface
    Route::get('/', function () {
        return view('messages.realtime');
    })->name('index');
    
    // Modern messaging interface
    Route::get('/modern', function () {
        return view('messages.modern');
    })->name('modern');
    
    // Enhanced modern messaging interface
    Route::get('/enhanced', [App\Http\Controllers\MessagingController::class, 'index'])->name('enhanced');
    Route::get('/enhanced-app', function () {
        return view('messages.modern-enhanced');
    })->name('enhanced-app');
    
// Developer-only messaging test routes (local only)
if (app()->environment('local')) {
    // Test route with auto-login for enhanced interface
    Route::get('/enhanced-test', function () {
        $user = App\Models\User::first();
        if ($user) {
            Auth::login($user);
            return view('messages.modern-enhanced');
        }
        return 'No users found';
    })->name('enhanced-test');

    // Test routes (should be removed in production)
    Route::get('/modern-test', function () {
        $user = App\Models\User::first();
        if ($user) {
            Auth::login($user);
            return view('messages.modern');
        }
        return 'No users found';
    })->name('modern.test');

    Route::get('/debug', function () {
        $user = App\Models\User::first();
        if ($user) {
            Auth::login($user);
            return view('messages.debug');
        }
        return 'No users found';
    })->name('debug');
}
    
    // API endpoints for messaging
    Route::get('/conversations', [App\Http\Controllers\MessagingController::class, 'getConversations'])->name('conversations');
    Route::get('/conversations/{conversationId}/messages', [App\Http\Controllers\MessagingController::class, 'getMessages'])->name('conversation.messages');
    Route::post('/send', [App\Http\Controllers\MessagingController::class, 'sendMessage'])->name('send');
    Route::post('/messages/{messageId}/read', [App\Http\Controllers\MessagingController::class, 'markAsRead'])->name('message.read');
    Route::post('/typing', [App\Http\Controllers\MessagingController::class, 'updateTyping'])->name('typing');
    Route::get('/conversations/{conversationId}/typing', [App\Http\Controllers\MessagingController::class, 'getTypingIndicators'])->name('conversation.typing');
    Route::post('/status', [App\Http\Controllers\MessagingController::class, 'updateOnlineStatus'])->name('status');
    Route::get('/online-users', [App\Http\Controllers\MessagingController::class, 'getOnlineUsers'])->name('online-users');
    Route::get('/search-users', [App\Http\Controllers\MessagingController::class, 'searchUsers'])->name('search-users');
    Route::post('/messages/{messageId}/reaction', [App\Http\Controllers\MessagingController::class, 'addReaction'])->name('message.reaction');
    
    // Additional messaging views
    Route::get('/conversations/{id}', function ($id) {
        return view('messages.app', ['conversationId' => $id]);
    })->name('conversation');
    
    Route::get('/create/{userId?}', function ($userId = null) {
        return view('messages.app', ['createConversationWithUserId' => $userId]);
    })->name('create');
    
    // User availability routes
    Route::get('/availability', [UserAvailabilityController::class, 'index'])->name('availability.index');
    Route::get('/availability/edit', [UserAvailabilityController::class, 'edit'])->name('availability.edit');
    Route::put('/availability', [UserAvailabilityController::class, 'update'])->name('availability.update');
    Route::post('/availability/template', [UserAvailabilityController::class, 'createTemplate'])->name('availability.template');
    Route::post('/availability/copy-day', [UserAvailabilityController::class, 'copyDay'])->name('availability.copy-day');
    Route::get('/availability/timezone-aware', function() {
        $users = \App\Models\User::whereHas('availability', function($query) {
            $query->where('is_available', true);
        })->with(['userType', 'availability'])->paginate(10);
        
        $timezones = \App\Models\UserAvailability::getCommonTimezones();
        $userTimezone = auth()->user()->timezone ?? 'UTC';
        
        return view('availability.timezone-aware', compact('users', 'timezones', 'userTimezone'));
    })->name('availability.timezone-aware');
    Route::get('/api/availability/{userId}', [UserAvailabilityController::class, 'getAvailability'])->name('api.availability.get');
    Route::get('/api/availability/bulk', [UserAvailabilityController::class, 'getBulkAvailability'])->name('api.availability.bulk');
    
    // Timezone-aware availability routes
    Route::get('/profile/availability', [UserTimezoneAvailabilityController::class, 'index'])->name('profile.availability');
    Route::put('/profile/availability', [UserTimezoneAvailabilityController::class, 'update'])->name('profile.availability.update');
    Route::post('/profile/availability/copy-day', [UserTimezoneAvailabilityController::class, 'copyDay'])->name('profile.availability.copy-day');
    Route::post('/profile/availability/template', [UserTimezoneAvailabilityController::class, 'createTemplate'])->name('profile.availability.template');
    
    // API endpoints for timezone availability
    Route::get('/api/users/{user}/availability', [UserTimezoneAvailabilityController::class, 'getAvailabilityInTimezone'])->name('api.users.availability');
    Route::get('/api/availability/bulk-timezone', [UserTimezoneAvailabilityController::class, 'getBulkAvailability'])->name('api.availability.bulk-timezone');
    Route::get('/api/users/search-availability', [UserTimezoneAvailabilityController::class, 'searchByAvailability'])->name('api.users.search-availability');
    
    // Timezone availability browser for agencies
    Route::get('/marketplace/timezone-availability', function() {
        return view('marketplace.timezone-availability');
    })->name('marketplace.timezone-availability');
    
    // WebRTC routes for video/audio calls (keep in messages for messaging integration)
    Route::post('/api/webrtc/signal', [\App\Http\Controllers\WebRTCController::class, 'signal'])->name('webrtc.signal');
    Route::get('/api/webrtc/signals', [\App\Http\Controllers\WebRTCController::class, 'getSignals'])->name('webrtc.get-signals');
    Route::post('/api/webrtc/initiate-call', [\App\Http\Controllers\WebRTCController::class, 'initiateCall'])->name('webrtc.initiate-call');
    Route::get('/api/webrtc/incoming-calls', [\App\Http\Controllers\WebRTCController::class, 'checkIncomingCalls'])->name('webrtc.incoming-calls');
    Route::post('/api/webrtc/respond-call', [\App\Http\Controllers\WebRTCController::class, 'respondToCall'])->name('webrtc.respond-call');
    Route::post('/api/webrtc/end-call', [\App\Http\Controllers\WebRTCController::class, 'endCall'])->name('webrtc.end-call');
});

// KYC routes - separate from messaging
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/kyc', [KycController::class, 'index'])->name('kyc.index');
    Route::get('/kyc/create', [KycController::class, 'create'])->name('kyc.create');
    Route::post('/kyc', [KycController::class, 'store'])->name('kyc.store');
    Route::get('/kyc/{kyc}', [KycController::class, 'show'])->name('kyc.show');
    Route::get('/kyc/{id}/download/{type}', [KycController::class, 'downloadFile'])->name('kyc.download');
});

// Rating and Review Contest routes - separate from messaging
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/ratings', [RatingController::class, 'index'])->name('ratings.index');
    Route::get('/ratings/create', [RatingController::class, 'create'])->name('ratings.create');
    Route::post('/ratings', [RatingController::class, 'store'])->name('ratings.store');
    Route::get('/ratings/{rating}', [RatingController::class, 'show'])->name('ratings.show');
    Route::get('/ratings/{rating}/edit', [RatingController::class, 'edit'])->name('ratings.edit');
    Route::put('/ratings/{rating}', [RatingController::class, 'update'])->name('ratings.update');
    Route::delete('/ratings/{rating}', [RatingController::class, 'destroy'])->name('ratings.destroy');
    
    // Review contest routes
    Route::get('/ratings/contests', [ReviewContestController::class, 'index'])->name('ratings.contests.index');
    Route::get('/ratings/{rating}/contest', [ReviewContestController::class, 'create'])->name('ratings.contests.create');
    Route::post('/ratings/{rating}/contest', [ReviewContestController::class, 'store'])->name('ratings.contests.store');
    Route::get('/ratings/contests/{contest}', [ReviewContestController::class, 'show'])->name('ratings.contests.show');
    Route::post('/ratings/contests/{contest}/cancel', [ReviewContestController::class, 'cancel'])->name('ratings.contests.cancel');
});

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
    
    // User type change request routes
    Route::get('/user-type-change/create', [UserTypeChangeController::class, 'create'])->name('user-type-change.create');
    Route::post('/user-type-change', [UserTypeChangeController::class, 'store'])->name('user-type-change.store');
    Route::get('/user-type-change/status', [UserTypeChangeController::class, 'show'])->name('user-type-change.show');
    Route::post('/user-type-change/cancel', [UserTypeChangeController::class, 'cancel'])->name('user-type-change.cancel');
    Route::get('/user-type-change/{userTypeChangeRequest}/document/{documentIndex}', [UserTypeChangeController::class, 'downloadDocument'])->name('user-type-change.download-document');
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
Route::middleware(['auth', 'verified', 'agency.only'])->prefix('agency')->name('agency.')->group(function () {
    Route::get('/employees', [AgencyManagementController::class, 'myEmployees'])->name('employees.index');
    Route::post('/contracts/{contract}/terminate', [AgencyManagementController::class, 'terminateContract'])->name('contracts.terminate');
    Route::post('/shifts/{shift}/review', [AgencyManagementController::class, 'reviewShift'])->name('shifts.review');
});

// Additional authenticated routes
Route::middleware(['auth', 'verified'])->group(function () {
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
    Route::post('/subscription/payment/success', [SubscriptionController::class, 'paymentSuccess'])->name('subscription.payment.success.post');
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

// Email verification routes - Handled by Wave
// Route::middleware('auth')->group(function () {
//     Route::get('/email/verify', [EmailVerificationController::class, 'notice'])
//         ->name('verification.notice');
//     
//     Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
//         ->middleware(['signed', 'throttle:6,1'])
//         ->name('verification.verify');
//     
//     Route::post('/email/verification-notification', [EmailVerificationController::class, 'resend'])
//         ->middleware('throttle:6,1')
//         ->name('verification.send');
// });

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

// User status route (for real-time presence)
Route::get('/users/{user}/status', [UserController::class, 'status'])->name('users.status')->middleware('auth');

// Test route for easy public profile access
if (app()->environment('local')) {
Route::get('/test-public-profile', function () {
    return redirect()->route('profile.public', ['user' => 5]);
})->name('test.public.profile');
}

// Developer-only: Job Application Test Page
if (app()->environment('local')) {
Route::get('/test-job-applications', function () {
    return '\n    <!DOCTYPE html>\n    <html>
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
}

// Developer-only: Simple working route
if (app()->environment('local')) {
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
            .btn { background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 5px; display: inline-block; }
            .btn:hover { background: #0056b3; }
            .btn.success { background: #28a745; }
            .section { margin: 20px 0; padding: 20px; background: #f8f9fa; border-radius: 8px; }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>🎉 IT WORKS!</h1>
            <p>Your Laravel application is running correctly.</p>
            <p><strong>Current Time:</strong> ' . date('Y-m-d H:i:s') . '</p>
            
            <div class="section">
                <h3>🔧 Test Navigation</h3>
                <p>Use these links to test different parts of the application:</p>
                <a href="/test-login" class="btn">Auto Login</a>
                <a href="/marketplace" class="btn">Marketplace</a>
                <a href="/messages/debug" class="btn success">Test Messages Debug</a>
                <a href="/messages/modern-test" class="btn success">Test Modern Messages</a>
                <a href="/login-max" class="btn">Login as Max</a>
            </div>
            
            <div class="section">
                <h3>📧 Messaging Tests</h3>
                <p>These routes will automatically log you in and test the messaging system:</p>
                <a href="/messages/debug" class="btn">Messages Debug Interface</a>
                <a href="/messages/modern-test" class="btn">Modern Messaging Interface</a>
                <a href="/test-enhanced-messaging" class="btn success">🚀 Enhanced Modern Interface</a>
                <a href="/demo-enhanced-messaging" class="btn success">🎯 Demo Interface (Full Features)</a>
                <a href="/production-messaging" class="btn success">⭐ PRODUCTION Interface</a>
                <a href="/messages/upgraded" class="btn success">🚀 UPGRADED Modern Interface</a>
                <p><small>Note: All routes will auto-login with the first user found in the database.</small></p>
            </div>
            
            <div class="section">
                <h3>ℹ️ Status</h3>
                <p>✅ Laravel server is running</p>
                <p>✅ Routes are accessible</p>
                <p>✅ Database has ' . App\Models\User::count() . ' users</p>
                <p>✅ CSRF protection is enabled</p>
                <p>✅ Authentication system is working</p>
            </div>
        </div>
    </body>
    </html>
    ';
});
}

// API test route
Route::get('/api-test', function () {
    return view('api-test');
});

// Developer-only: Test messages without auth for debugging
if (app()->environment('local')) {
Route::get('/test-messages', function () {
    try {
        // Auto login as first user
        $user = App\Models\User::first();
        if ($user) {
            Auth::login($user);
        }
        
        // Create a simple HTML response instead of using the view
        return '<!DOCTYPE html>
        <html>
        <head>
            <title>Messages Test</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }
                .container { max-width: 1000px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; }
                .success { color: green; }
                .error { color: red; }
            </style>
        </head>
        <body>
            <div class="container">
                <h1>📧 Messages Route Test</h1>
                <p class="success">✅ Route is working!</p>
                <p><strong>Authenticated User:</strong> ' . (auth()->check() ? auth()->user()->name . ' (' . auth()->user()->email . ')' : 'Not authenticated') . '</p>
                <p><strong>Timestamp:</strong> ' . now()->format('Y-m-d H:i:s') . '</p>
                <hr>
                <h3>Next Steps:</h3>
                <ul>
                    <li>The `/test-messages` route is accessible and working</li>
                    <li>The issue with the real `/messages` route is likely in the view or controller</li>
                    <li>You can now check the MarketplaceController::messages method</li>
                    <li>Or check if the messages.index view has compilation issues</li>
                </ul>
                <p><a href="/messages">Try Real Messages Route</a> | <a href="/marketplace/messages">Try Marketplace Messages</a></p>
            </div>
        </body>
        </html>';
        
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
});
}

// Developer-only: AJAX test route
if (app()->environment('local')) {
Route::post('/test-ajax', [JobController::class, 'testAjax'])->name('test.ajax');
}

// Developer-only: Test job posting route without middleware restrictions
if (app()->environment('local')) {
Route::post('/test-job-post', [JobController::class, 'testJobPost'])->name('test.job.post')->middleware(['auth']);
}

// Developer-only: Diagnostic route to help debug issues
if (app()->environment('local')) {
Route::get('/debug-job-status/{job?}', function(Request $request, $jobId = 1) {
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
}

// Developer-only: Debug job application test route
if (app()->environment('local')) {
Route::get('/debug-job-test', function() {
    try {
        // Test with User ID 2 (Test User for Admin Panel) who is KYC verified
        $user = App\Models\User::find(2);
        $job = App\Models\JobPost::find(1);
        
        if (!$user || !$job) {
            return response()->json(['error' => 'User or Job not found']);
        }
        
        $output = [];
        $output[] = '=== JOB APPLICATION TEST ===';
        $output[] = 'User: ' . $user->name . ' (ID: ' . $user->id . ')';
        $output[] = 'Job: ' . $job->title . ' (ID: ' . $job->id . ')';
        $output[] = 'Job Owner: ' . $job->user->name . ' (ID: ' . $job->user_id . ')';
        $output[] = '';
        $output[] = '=== PRE-CHECKS ===';
        
        // Check 1: User type authorization
        $output[] = 'isChatter(): ' . ($user->isChatter() ? 'true' : 'false');
        $output[] = 'canApplyToJobs(): ' . ($user->canApplyToJobs() ? 'true' : 'false');
        
        // Check 2: Verification status
        $output[] = 'requiresVerification(): ' . ($user->requiresVerification() ? 'true' : 'false');
        $output[] = 'isKycVerified(): ' . ($user->isKycVerified() ? 'true' : 'false');
        
        // Check 3: Already applied?
        $existingApp = App\Models\JobApplication::where('job_post_id', 1)->where('user_id', 2)->first();
        $output[] = 'Already applied: ' . ($existingApp ? 'YES (ID: ' . $existingApp->id . ')' : 'NO');
        
        // Check 4: Job full?
        $output[] = 'Job applications: ' . $job->current_applications . '/' . $job->max_applications;
        $output[] = 'Job full: ' . (($job->current_applications >= $job->max_applications) ? 'YES' : 'NO');
        
        // Check 5: Own job?
        $output[] = 'Is own job: ' . (($job->user_id === $user->id) ? 'YES' : 'NO');
        
        // Check 6: Subscription limits
        $output[] = 'canApplyToJob(): ' . ($user->canApplyToJob() ? 'true' : 'false');
        
        $output[] = '';
        $output[] = '=== CONTROLLER SIMULATION ===';
        
        // Simulate controller checks step by step
        if ($user->isAgency()) {
            $output[] = 'BLOCKED: User is agency';
        } elseif ($existingApp) {
            $output[] = 'BLOCKED: Already applied';
        } elseif ($job->current_applications >= $job->max_applications) {
            $output[] = 'BLOCKED: Job is full';
        } elseif ($job->user_id === $user->id) {
            $output[] = 'BLOCKED: Own job';
        } elseif (!$user->canApplyToJob()) {
            $output[] = 'BLOCKED: Subscription limit reached';
        } elseif ($user->requiresVerification()) {
            $output[] = 'BLOCKED: KYC verification required';
        } else {
            $output[] = 'CHECKS PASSED: Should be able to apply';
        }
        
        return response()->json([
            'success' => true,
            'output' => $output
        ]);
        
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }
});
}

// Developer-only: Debug job application route - no middleware restrictions
if (app()->environment('local')) {
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
}

// API routes for micro-app validation (no auth middleware as JWT handles it)
Route::prefix('api/messaging-app')->name('api.messaging-app.')->group(function () {
    Route::post('/validate-token', [App\Http\Controllers\MessagingMicroAppController::class, 'validateToken'])->name('validate-token');
});

// Developer-only standalone messaging demo routes (local only)
if (app()->environment('local')) {
Route::get('/test-enhanced-messaging', function () {
    $user = App\Models\User::first();
    if ($user) {
        Auth::login($user);
        return view('messages.modern-enhanced');
    }
    return 'No users found';
})->name('test.enhanced.messaging');

// Demo messaging interface with working data and interactions
Route::get('/demo-enhanced-messaging', function () {
    $user = App\Models\User::first();
    if ($user) {
        Auth::login($user);
        return view('messages.demo-enhanced');
    }
    return 'No users found';
})->name('demo.enhanced.messaging');

// Production messaging interface
Route::get('/production-messaging', function () {
    $user = App\Models\User::first();
    if ($user) {
        Auth::login($user);
        return view('messages.production');
    }
    return 'No users found';
})->name('production.messaging');

// Upgraded modern messaging interface
Route::get('/upgraded', function () {
    $user = App\Models\User::first();
    if ($user) {
        Auth::login($user);
        return view('messages.upgraded');
    }
    return 'No users found';
})->name('upgraded.messaging');

// Real-time WebSocket messaging interface
Route::get('/realtime', function () {
    $user = App\Models\User::first();
    if ($user) {
        Auth::login($user);
        return view('messages.realtime');
    }
    return 'No users found';
})->name('realtime.messaging');
}
