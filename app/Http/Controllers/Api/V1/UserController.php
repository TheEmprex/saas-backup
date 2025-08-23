<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\BaseController;
use App\Models\User;
use App\Services\LoggingService;
use App\Services\CachingService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;

class UserController extends BaseController
{
    protected CachingService $cache;

    public function __construct(LoggingService $logger, CachingService $cache)
    {
        parent::__construct($logger);
        $this->cache = $cache;
    }

    /**
     * Search users for conversations
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'q' => 'required|string|min:1|max:255',
                'per_page' => 'integer|min:1|max:50',
                'exclude_current' => 'boolean',
                'user_type' => 'nullable|in:all,chatter,agency,admin'
            ]);

            if ($validator->fails()) {
                return $this->validationErrorResponse($validator->errors());
            }

            $query = $request->q;
            $perPage = $request->get('per_page', 20);
            $excludeCurrent = $request->get('exclude_current', true);
            $userType = $request->get('user_type', 'all');

            $cacheKey = "user_search:" . md5($query . $perPage . $excludeCurrent . $userType . auth()->id());
            
            $users = Cache::remember($cacheKey, 300, function () use ($query, $perPage, $excludeCurrent, $userType) {
                $userQuery = User::select('id', 'name', 'email', 'avatar', 'is_online', 'last_seen_at', 'user_type_id')
                    ->with('userType:id,name')
                    ->where(function ($q) use ($query) {
                        $q->where('name', 'like', "%{$query}%")
                          ->orWhere('email', 'like', "%{$query}%");
                    })
                    ->where('email_verified_at', '!=', null); // Only verified users

                if ($excludeCurrent) {
                    $userQuery->where('id', '!=', auth()->id());
                }

                // Filter by user type
                if ($userType !== 'all') {
                    $userQuery->whereHas('userType', function ($q) use ($userType) {
                        $q->where('name', $userType);
                    });
                }

                return $userQuery->orderBy('name')
                    ->paginate($perPage);
            });

            // Transform users for response
            $users->getCollection()->transform(function ($user) {
                return $this->transformUser($user);
            });

            $this->logActivity('users_searched', [
                'query' => $query,
                'user_type' => $userType,
                'results_count' => $users->count()
            ]);

            return $this->paginatedResponse($users, 'Users found successfully');

        } catch (\Exception $e) {
            return $this->handleException($e, 'searching_users');
        }
    }

    /**
     * Get user profile information
     */
    public function show(User $user): JsonResponse
    {
        try {
            $user->load(['userType:id,name', 'userProfile']);

            return $this->successResponse(
                $this->transformUser($user, true),
                'User profile retrieved successfully'
            );

        } catch (\Exception $e) {
            return $this->handleException($e, 'getting_user_profile');
        }
    }

    /**
     * Get online users
     */
    public function getOnlineUsers(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'per_page' => 'integer|min:1|max:100'
            ]);

            if ($validator->fails()) {
                return $this->validationErrorResponse($validator->errors());
            }

            $perPage = $request->get('per_page', 50);

            $users = User::select('id', 'name', 'email', 'avatar', 'is_online', 'last_seen_at')
                ->where('is_online', true)
                ->where('id', '!=', auth()->id())
                ->orderBy('last_seen_at', 'desc')
                ->paginate($perPage);

            $users->getCollection()->transform(function ($user) {
                return $this->transformUser($user);
            });

            return $this->paginatedResponse($users, 'Online users retrieved successfully');

        } catch (\Exception $e) {
            return $this->handleException($e, 'getting_online_users');
        }
    }

    /**
     * Update user's online status
     */
    public function updateOnlineStatus(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'is_online' => 'required|boolean'
            ]);

            if ($validator->fails()) {
                return $this->validationErrorResponse($validator->errors());
            }

            $user = auth()->user();
            $user->update([
                'is_online' => $request->is_online,
                'last_seen_at' => now()
            ]);

            $this->logActivity('online_status_updated', [
                'is_online' => $request->is_online
            ]);

            return $this->successResponse([
                'is_online' => $user->is_online,
                'last_seen_at' => $user->last_seen_at
            ], 'Online status updated successfully');

        } catch (\Exception $e) {
            return $this->handleException($e, 'updating_online_status');
        }
    }

    /**
     * Get user's recent activity
     */
    public function getRecentActivity(User $user): JsonResponse
    {
        try {
            // Check if user has permission to view this activity
            if ($user->id !== auth()->id() && !auth()->user()->isAdmin()) {
                return $this->forbiddenResponse('You do not have permission to view this user\'s activity');
            }

            $activities = collect([
                // Get recent messages
                'messages' => $user->sentMessages()
                    ->with('conversation:id,title')
                    ->latest()
                    ->limit(5)
                    ->get()
                    ->map(function ($message) {
                        return [
                            'type' => 'message',
                            'action' => 'sent_message',
                            'data' => [
                                'conversation_title' => $message->conversation->title,
                                'content_preview' => \Str::limit($message->content, 50)
                            ],
                            'created_at' => $message->created_at
                        ];
                    }),

                // Get recent conversations
                'conversations' => $user->conversations()
                    ->latest('conversation_participants.created_at')
                    ->limit(3)
                    ->get()
                    ->map(function ($conversation) {
                        return [
                            'type' => 'conversation',
                            'action' => 'joined_conversation',
                            'data' => [
                                'conversation_title' => $conversation->title,
                                'conversation_type' => $conversation->type
                            ],
                            'created_at' => $conversation->pivot->created_at
                        ];
                    })
            ]);

            // Flatten and sort activities
            $allActivities = $activities->flatten(1)
                ->sortByDesc('created_at')
                ->take(10)
                ->values();

            return $this->successResponse($allActivities, 'Recent activity retrieved successfully');

        } catch (\Exception $e) {
            return $this->handleException($e, 'getting_user_activity');
        }
    }

    // Protected helper methods

    protected function transformUser(User $user, bool $detailed = false): array
    {
        $data = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $detailed ? $user->email : null,
            'avatar' => $user->avatar,
            'is_online' => $user->is_online ?? false,
            'last_seen_at' => $user->last_seen_at,
            'user_type' => $user->userType ? [
                'id' => $user->userType->id,
                'name' => $user->userType->name,
            ] : null,
        ];

        if ($detailed && $user->userProfile) {
            $data['profile'] = [
                'bio' => $user->userProfile->bio,
                'location' => $user->userProfile->location,
                'timezone' => $user->userProfile->timezone,
                'skills' => $user->userProfile->skills ? json_decode($user->userProfile->skills, true) : [],
                'hourly_rate' => $user->userProfile->hourly_rate,
                'availability_status' => $user->userProfile->availability_status,
            ];
        }

        return $data;
    }
}
