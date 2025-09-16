<?php

declare(strict_types=1);

use Illuminate\Http\Request;
use App\Http\Controllers\Api\JobPostController;
use App\Http\Controllers\Api\JobApplicationController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\MessageFolderController;
use App\Http\Controllers\Api\UserProfileController;
use App\Http\Controllers\Api\FileUploadController;

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
    
// Enhanced Messaging System - v1 API Routes
    Route::prefix('v1')->name('v1.')->group(function () {
        // Conversations
        Route::get('conversations', [\App\Http\Controllers\Api\V1\ConversationController::class, 'index']);
        Route::post('conversations', [\App\Http\Controllers\Api\V1\ConversationController::class, 'store']);
        Route::get('conversations/{conversation}', [\App\Http\Controllers\Api\V1\ConversationController::class, 'show']);
        Route::patch('conversations/{conversation}', [\App\Http\Controllers\Api\V1\ConversationController::class, 'update']);
        Route::delete('conversations/{conversation}', [\App\Http\Controllers\Api\V1\ConversationController::class, 'destroy']);
        Route::post('conversations/{conversation}/archive', [\App\Http\Controllers\Api\V1\ConversationController::class, 'archive']);
        Route::post('conversations/{conversation}/unarchive', [\App\Http\Controllers\Api\V1\ConversationController::class, 'unarchive']);
        Route::post('conversations/{conversation}/mute', [\App\Http\Controllers\Api\V1\ConversationController::class, 'mute']);
        Route::post('conversations/{conversation}/unmute', [\App\Http\Controllers\Api\V1\ConversationController::class, 'unmute']);
        Route::post('conversations/{conversation}/star', [\App\Http\Controllers\Api\V1\ConversationController::class, 'star']);
        Route::delete('conversations/{conversation}/star', [\App\Http\Controllers\Api\V1\ConversationController::class, 'unstar']);
        
        // Messages
        Route::get('conversations/{conversation}/messages', [\App\Http\Controllers\Api\V1\MessageController::class, 'index']);
        Route::post('conversations/{conversation}/messages', [\App\Http\Controllers\Api\V1\MessageController::class, 'store']);
        Route::get('messages/{message}', [\App\Http\Controllers\Api\V1\MessageController::class, 'show']);
        Route::patch('messages/{message}', [\App\Http\Controllers\Api\V1\MessageController::class, 'update']);
        Route::delete('messages/{message}', [\App\Http\Controllers\Api\V1\MessageController::class, 'destroy']);
        Route::patch('conversations/{conversation}/messages/read', [\App\Http\Controllers\Api\V1\MessageController::class, 'markAsRead']);
        Route::post('messages/{message}/reactions', [\App\Http\Controllers\Api\V1\MessageController::class, 'addReaction']);
        Route::delete('messages/{message}/reactions/{reaction}', [\App\Http\Controllers\Api\V1\MessageController::class, 'removeReaction']);
        Route::post('messages/{message}/reply', [\App\Http\Controllers\Api\V1\MessageController::class, 'reply']);
        Route::post('messages/{message}/forward', [\App\Http\Controllers\Api\V1\MessageController::class, 'forward']);
        
        // File uploads
        Route::post('conversations/{conversation}/files', [\App\Http\Controllers\Api\V1\MessageController::class, 'uploadFile']);
        Route::post('conversations/{conversation}/images', [\App\Http\Controllers\Api\V1\MessageController::class, 'uploadImage']);
        Route::post('conversations/{conversation}/voice', [\App\Http\Controllers\Api\V1\MessageController::class, 'uploadVoice']);
        
        // Real-time features
        Route::post('conversations/{conversation}/typing', [\App\Http\Controllers\Api\V1\ConversationController::class, 'typing']);
        Route::get('conversations/{conversation}/typing', [\App\Http\Controllers\Api\V1\ConversationController::class, 'getTyping']);
        Route::post('conversations/{conversation}/online', [\App\Http\Controllers\Api\V1\ConversationController::class, 'updateOnlineStatus']);
        Route::get('conversations/{conversation}/participants', [\App\Http\Controllers\Api\V1\ConversationController::class, 'getParticipants']);
        
        // Search and discovery
        Route::get('users/search', [\App\Http\Controllers\Api\V1\UserController::class, 'search']);
        Route::get('conversations/search', [\App\Http\Controllers\Api\V1\ConversationController::class, 'search']);
        Route::get('messages/search', [\App\Http\Controllers\Api\V1\MessageController::class, 'search']);
        
        // System health and monitoring
        Route::get('system/health', [\App\Http\Controllers\Api\V1\SystemController::class, 'health']);
        Route::get('system/metrics', [\App\Http\Controllers\Api\V1\SystemController::class, 'metrics']);
        Route::get('system/logs', [\App\Http\Controllers\Api\V1\SystemController::class, 'logs']);

        // Web Push Subscriptions
        Route::post('webpush/subscriptions', [\App\Http\Controllers\Api\V1\WebPushSubscriptionController::class, 'store']);
        Route::delete('webpush/subscriptions', [\App\Http\Controllers\Api\V1\WebPushSubscriptionController::class, 'destroy']);
        Route::post('webpush/test', [\App\Http\Controllers\Api\V1\WebPushSubscriptionController::class, 'test']);

        // Notification preferences
        Route::get('notifications/preferences', [\App\Http\Controllers\Api\V1\NotificationPreferencesController::class, 'show']);
        Route::put('notifications/preferences', [\App\Http\Controllers\Api\V1\NotificationPreferencesController::class, 'update']);
    });
    
    // Legacy routes (for backward compatibility)
    Route::get('conversations', [\App\Http\Controllers\ConversationController::class, 'index']);
    Route::post('conversations', [\App\Http\Controllers\ConversationController::class, 'store']);
    Route::get('conversations/{id}', [\App\Http\Controllers\ConversationController::class, 'show']);
    Route::patch('conversations/{id}', [\App\Http\Controllers\ConversationController::class, 'update']);
    Route::delete('conversations/{id}', [\App\Http\Controllers\ConversationController::class, 'destroy']);
    Route::get('users/search', [\App\Http\Controllers\ConversationController::class, 'searchUsers']);
    
    // Messages
    Route::get('conversations/{conversationId}/messages', [\App\Http\Controllers\MessageController::class, 'index']);
    Route::post('messages', [\App\Http\Controllers\MessageController::class, 'store']);
    Route::patch('messages/{id}', [\App\Http\Controllers\MessageController::class, 'update']);
    Route::delete('messages/{id}', [\App\Http\Controllers\MessageController::class, 'destroy']);
    Route::patch('messages/{id}/read', [\App\Http\Controllers\MessageController::class, 'markAsRead']);
    Route::post('messages/{id}/reactions', [\App\Http\Controllers\MessageController::class, 'addReaction']);
    Route::post('messages/upload', [\App\Http\Controllers\MessageController::class, 'uploadFile']);
    
    // File Upload routes for messaging
    Route::post('messages/upload-files', [FileUploadController::class, 'uploadMessageFiles']);
    Route::get('files/{conversationId}/{filename}', [FileUploadController::class, 'getFile']);
    Route::delete('files/delete', [FileUploadController::class, 'deleteFile']);
    
// Online users for messaging store
Route::middleware('auth:api')->get('users/online', [\App\Http\Controllers\Api\V1\UserController::class, 'getOnlineUsers']);

// Message folder routes - consolidated
    Route::prefix('message-folders')->group(function () {
        Route::get('/', [MessageFolderController::class, 'apiIndex']);
        Route::post('/', [MessageFolderController::class, 'apiStore']);
        Route::get('/{folder}', [MessageFolderController::class, 'apiShow']);
        Route::put('/{folder}', [MessageFolderController::class, 'apiUpdate']);
        Route::patch('/{folder}', [MessageFolderController::class, 'update']);
        Route::delete('/{folder}', [MessageFolderController::class, 'apiDestroy']);
        Route::get('/{folder}/messages', [MessageFolderController::class, 'messages']);
        Route::get('/{folder}/conversations', [MessageFolderController::class, 'conversations']);
        Route::post('/{folder}/conversations', [MessageFolderController::class, 'addConversation']);
        Route::delete('/{folder}/conversations/{conversationId}', [MessageFolderController::class, 'removeConversation']);
        Route::post('/{folder}/move', [MessageFolderController::class, 'moveMessages']);
        Route::post('/reorder', [MessageFolderController::class, 'reorder']);
    });
    
    // User Profile
    Route::apiResource('user-profiles', UserProfileController::class);
    Route::get('profile/me', [UserProfileController::class, 'me']);
    Route::patch('profile/me', [UserProfileController::class, 'updateMe']);
    
    // Statistics and Analytics
    Route::get('stats/dashboard', function () {
        $user = auth()->user();
        $userProfile = App\Models\UserProfile::where('user_id', $user->id)->first();
        
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
