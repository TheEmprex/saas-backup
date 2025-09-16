<?php

declare(strict_types=1);

use App\Http\Controllers\Api\JobApplicationController;
use App\Http\Controllers\Api\JobPostController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\UserProfileController;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', fn (Request $request) => auth()->user());

Wave::api();

// Posts Example API Route
Route::group(['middleware' => 'auth:api'], function (): void {
    Route::get('posts', '\App\Http\Controllers\Api\ApiController@posts');
});

// Marketplace API Routes
Route::group(['middleware' => 'auth:api', 'prefix' => 'marketplace'], function (): void {

    // Job Posts
    Route::apiResource('job-posts', JobPostController::class);
    Route::get('my-jobs', [JobPostController::class, 'myJobs']);
    Route::get('job-posts/{jobPost}/applications', [JobPostController::class, 'applications']);

    // Job Applications
    Route::apiResource('job-applications', JobApplicationController::class);
    Route::get('applications/received', [JobApplicationController::class, 'received']);
    Route::patch('applications/bulk-update', [JobApplicationController::class, 'bulkUpdate']);

    // Messages
    Route::apiResource('messages', MessageController::class);
    Route::get('conversations', [MessageController::class, 'index']);
    Route::get('conversations/{user}', [MessageController::class, 'conversation']);
    Route::patch('messages/{message}/read', [MessageController::class, 'markAsRead']);
    Route::patch('conversations/{user}/read', [MessageController::class, 'markConversationAsRead']);
    Route::get('messages/unread/count', [MessageController::class, 'unreadCount']);
    Route::get('messages/search', [MessageController::class, 'search']);

    // User Profile
    Route::apiResource('user-profiles', UserProfileController::class);
    Route::get('profile/me', [UserProfileController::class, 'me']);
    Route::patch('profile/me', [UserProfileController::class, 'updateMe']);

    // Statistics and Analytics
    Route::get('stats/dashboard', function () {
        $user = auth()->user();
        $userProfile = App\Models\UserProfile::query->where('user_id', $user->id)->first();

        return response()->json([
            'jobs_posted' => $user->jobPosts()->count(),
            'applications_sent' => $user->jobApplications()->count(),
            'applications_received' => $user->jobPosts()->withCount('applications')->get()->sum('applications_count'),
            'messages_sent' => $user->sentMessages()->count(),
            'messages_received' => $user->receivedMessages()->count(),
            'unread_messages' => $user->receivedMessages()->where('is_read', false)->count(),
            'profile_views' => $userProfile?->views ?? 0,
            'ratings_received' => $user->ratingsReceived()->count(),
            'average_rating' => $user->ratingsReceived()->avg('overall_rating') ?? 0,
        ]);
    });
});
