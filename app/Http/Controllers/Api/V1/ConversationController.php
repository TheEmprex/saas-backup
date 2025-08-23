<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\BaseController;
use App\Models\Conversation;
use App\Models\User;
use App\Services\LoggingService;
use App\Services\CachingService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ConversationController extends BaseController
{
    protected CachingService $cache;

    public function __construct(LoggingService $logger, CachingService $cache)
    {
        parent::__construct($logger);
        $this->cache = $cache;
    }

    /**
     * Get user's conversations with pagination and filtering
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $this->logActivity('viewing_conversations');

            $validator = Validator::make($request->all(), [
                'search' => 'string|max:255',
                'filter' => 'in:all,unread,starred,archived,muted',
                'per_page' => 'integer|min:1|max:100',
                'page' => 'integer|min:1'
            ]);

            if ($validator->fails()) {
                return $this->validationErrorResponse($validator->errors());
            }

            $perPage = $request->get('per_page', 15);
            $search = $request->get('search');
            $filter = $request->get('filter', 'all');
            
            $cacheKey = "conversations:user:{auth()->id()}:{$filter}:{$search}:" . $request->get('page', 1);
            
            $conversations = Cache::remember($cacheKey, 300, function () use ($search, $filter, $perPage) {
                $query = Conversation::with([
                    'participants:id,name,email,avatar',
                    'lastMessage:id,content,type,sender_id,created_at,conversation_id',
                    'lastMessage.sender:id,name,avatar'
                ])
                ->whereHas('participants', function ($q) {
                    $q->where('user_id', auth()->id());
                })
                ->withCount(['messages', 'unreadMessages' => function ($q) {
                    $q->whereDoesntHave('reads', function ($readQuery) {
                        $readQuery->where('user_id', auth()->id());
                    });
                }]);

                // Apply filters
                switch ($filter) {
                    case 'unread':
                        $query->having('unread_messages_count', '>', 0);
                        break;
                    case 'starred':
                        $query->whereHas('participants', function ($q) {
                            $q->where('user_id', auth()->id())
                              ->where('is_starred', true);
                        });
                        break;
                    case 'archived':
                        $query->whereHas('participants', function ($q) {
                            $q->where('user_id', auth()->id())
                              ->where('is_archived', true);
                        });
                        break;
                    case 'muted':
                        $query->whereHas('participants', function ($q) {
                            $q->where('user_id', auth()->id())
                              ->where('is_muted', true);
                        });
                        break;
                }

                // Apply search
                if ($search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('title', 'like', "%{$search}%")
                          ->orWhereHas('participants', function ($participantQuery) use ($search) {
                              $participantQuery->where('name', 'like', "%{$search}%");
                          })
                          ->orWhereHas('messages', function ($messageQuery) use ($search) {
                              $messageQuery->where('content', 'like', "%{$search}%");
                          });
                    });
                }

                return $query->orderBy('updated_at', 'desc')->paginate($perPage);
            });

            // Transform conversations for response
            $conversations->getCollection()->transform(function ($conversation) {
                return $this->transformConversation($conversation);
            });

            $this->logPerformance('conversations_listed', [
                'count' => $conversations->count(),
                'filter' => $filter,
                'has_search' => !empty($search)
            ]);

            return $this->paginatedResponse($conversations, 'Conversations retrieved successfully');

        } catch (\Exception $e) {
            return $this->handleException($e, 'listing_conversations');
        }
    }

    /**
     * Create a new conversation
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'participant_ids' => 'required|array|min:1|max:50',
                'participant_ids.*' => 'exists:users,id',
                'title' => 'nullable|string|max:255',
                'initial_message' => 'nullable|string|max:10000',
                'type' => 'in:direct,group'
            ]);

            if ($validator->fails()) {
                return $this->validationErrorResponse($validator->errors());
            }

            $participantIds = $request->participant_ids;
            $type = count($participantIds) > 1 ? 'group' : 'direct';
            
            // Add current user to participants if not included
            if (!in_array(auth()->id(), $participantIds)) {
                $participantIds[] = auth()->id();
            }

            // Check if direct conversation already exists
            if ($type === 'direct' && count($participantIds) === 2) {
                $existingConversation = Conversation::where('type', 'direct')
                    ->whereHas('participants', function ($q) use ($participantIds) {
                        $q->whereIn('user_id', $participantIds);
                    })
                    ->having(DB::raw('COUNT(DISTINCT conversation_participants.user_id)'), '=', 2)
                    ->first();

                if ($existingConversation) {
                    return $this->successResponse(
                        $this->transformConversation($existingConversation), 
                        'Existing conversation found'
                    );
                }
            }

            DB::beginTransaction();

            $conversation = Conversation::create([
                'title' => $request->title ?: $this->generateConversationTitle($participantIds),
                'type' => $type,
                'created_by' => auth()->id()
            ]);

            // Add participants
            $participantData = collect($participantIds)->map(function ($userId) {
                return [
                    'user_id' => $userId,
                    'joined_at' => now(),
                    'is_admin' => $userId === auth()->id()
                ];
            })->toArray();

            $conversation->participants()->attach($participantData);

            // Send initial message if provided
            if ($request->initial_message) {
                $conversation->messages()->create([
                    'sender_id' => auth()->id(),
                    'content' => $request->initial_message,
                    'type' => 'text'
                ]);
            }

            DB::commit();

            // Clear relevant caches
            $this->clearConversationCaches($participantIds);

            $conversation->load([
                'participants:id,name,email,avatar',
                'lastMessage:id,content,type,sender_id,created_at,conversation_id',
                'lastMessage.sender:id,name,avatar'
            ]);

            $this->logActivity('conversation_created', [
                'conversation_id' => $conversation->id,
                'type' => $type,
                'participants_count' => count($participantIds)
            ]);

            $this->logAudit('conversation_created', $conversation);

            return $this->createdResponse(
                $this->transformConversation($conversation),
                'Conversation created successfully'
            );

        } catch (\Exception $e) {
            DB::rollback();
            return $this->handleException($e, 'creating_conversation');
        }
    }

    /**
     * Get a specific conversation with messages
     */
    public function show(Conversation $conversation): JsonResponse
    {
        try {
            // Check if user is participant
            if (!$conversation->participants()->where('user_id', auth()->id())->exists()) {
                return $this->forbiddenResponse('You are not a participant in this conversation');
            }

            $conversation->load([
                'participants:id,name,email,avatar,last_seen_at,is_online',
                'messages' => function ($query) {
                    $query->with(['sender:id,name,avatar', 'reads', 'reactions.user:id,name'])
                          ->latest()
                          ->limit(50);
                }
            ]);

            $this->logActivity('conversation_viewed', ['conversation_id' => $conversation->id]);

            return $this->successResponse(
                $this->transformConversationDetailed($conversation),
                'Conversation retrieved successfully'
            );

        } catch (\Exception $e) {
            return $this->handleException($e, 'showing_conversation');
        }
    }

    /**
     * Update conversation details
     */
    public function update(Request $request, Conversation $conversation): JsonResponse
    {
        try {
            // Check if user is participant and has admin rights for group conversations
            $participant = $conversation->participants()->where('user_id', auth()->id())->first();
            
            if (!$participant) {
                return $this->forbiddenResponse('You are not a participant in this conversation');
            }

            if ($conversation->type === 'group' && !$participant->pivot->is_admin) {
                return $this->forbiddenResponse('Only administrators can update group conversations');
            }

            $validator = Validator::make($request->all(), [
                'title' => 'string|max:255',
                'description' => 'nullable|string|max:1000',
                'avatar' => 'nullable|image|max:2048'
            ]);

            if ($validator->fails()) {
                return $this->validationErrorResponse($validator->errors());
            }

            $oldData = $conversation->toArray();

            if ($request->has('title')) {
                $conversation->title = $request->title;
            }

            if ($request->has('description')) {
                $conversation->description = $request->description;
            }

            if ($request->hasFile('avatar')) {
                // Handle avatar upload
                $avatarPath = $request->file('avatar')->store('conversation_avatars', 'public');
                $conversation->avatar = $avatarPath;
            }

            $conversation->save();

            $this->logActivity('conversation_updated', ['conversation_id' => $conversation->id]);
            $this->logAudit('conversation_updated', $conversation, array_diff($conversation->toArray(), $oldData));

            return $this->successResponse(
                $this->transformConversation($conversation),
                'Conversation updated successfully'
            );

        } catch (\Exception $e) {
            return $this->handleException($e, 'updating_conversation');
        }
    }

    /**
     * Delete/leave a conversation
     */
    public function destroy(Conversation $conversation): JsonResponse
    {
        try {
            $participant = $conversation->participants()->where('user_id', auth()->id())->first();
            
            if (!$participant) {
                return $this->forbiddenResponse('You are not a participant in this conversation');
            }

            DB::beginTransaction();

            if ($conversation->type === 'direct') {
                // For direct conversations, just remove the user
                $conversation->participants()->detach(auth()->id());
                
                // If no participants left, delete the conversation
                if ($conversation->participants()->count() === 0) {
                    $conversation->delete();
                }
            } else {
                // For group conversations
                if ($participant->pivot->is_admin) {
                    // If admin is leaving, transfer admin rights or delete conversation
                    $otherParticipants = $conversation->participants()
                        ->where('user_id', '!=', auth()->id())
                        ->exists();
                    
                    if ($otherParticipants) {
                        // Transfer admin to first remaining participant
                        $newAdmin = $conversation->participants()
                            ->where('user_id', '!=', auth()->id())
                            ->first();
                        
                        $conversation->participants()
                            ->updateExistingPivot($newAdmin->id, ['is_admin' => true]);
                    } else {
                        // Delete conversation if no other participants
                        $conversation->delete();
                        DB::commit();
                        
                        $this->logActivity('conversation_deleted', ['conversation_id' => $conversation->id]);
                        return $this->deletedResponse('Conversation deleted successfully');
                    }
                }
                
                $conversation->participants()->detach(auth()->id());
                
                // Add system message about user leaving
                $conversation->messages()->create([
                    'sender_id' => null,
                    'content' => auth()->user()->name . ' left the conversation',
                    'type' => 'system'
                ]);
            }

            DB::commit();

            $this->clearConversationCaches([auth()->id()]);
            $this->logActivity('conversation_left', ['conversation_id' => $conversation->id]);

            return $this->deletedResponse('Successfully left conversation');

        } catch (\Exception $e) {
            DB::rollback();
            return $this->handleException($e, 'leaving_conversation');
        }
    }

    /**
     * Archive a conversation
     */
    public function archive(Conversation $conversation): JsonResponse
    {
        return $this->updateParticipantStatus($conversation, 'is_archived', true, 'archived');
    }

    /**
     * Unarchive a conversation
     */
    public function unarchive(Conversation $conversation): JsonResponse
    {
        return $this->updateParticipantStatus($conversation, 'is_archived', false, 'unarchived');
    }

    /**
     * Mute a conversation
     */
    public function mute(Conversation $conversation): JsonResponse
    {
        return $this->updateParticipantStatus($conversation, 'is_muted', true, 'muted');
    }

    /**
     * Unmute a conversation
     */
    public function unmute(Conversation $conversation): JsonResponse
    {
        return $this->updateParticipantStatus($conversation, 'is_muted', false, 'unmuted');
    }

    /**
     * Star a conversation
     */
    public function star(Conversation $conversation): JsonResponse
    {
        return $this->updateParticipantStatus($conversation, 'is_starred', true, 'starred');
    }

    /**
     * Unstar a conversation
     */
    public function unstar(Conversation $conversation): JsonResponse
    {
        return $this->updateParticipantStatus($conversation, 'is_starred', false, 'unstarred');
    }

    /**
     * Update typing status
     */
    public function typing(Request $request, Conversation $conversation): JsonResponse
    {
        try {
            if (!$conversation->participants()->where('user_id', auth()->id())->exists()) {
                return $this->forbiddenResponse('You are not a participant in this conversation');
            }

            $validator = Validator::make($request->all(), [
                'is_typing' => 'required|boolean'
            ]);

            if ($validator->fails()) {
                return $this->validationErrorResponse($validator->errors());
            }

            $cacheKey = "typing:{$conversation->id}:" . auth()->id();
            
            if ($request->is_typing) {
                Cache::put($cacheKey, [
                    'user_id' => auth()->id(),
                    'user_name' => auth()->user()->name,
                    'timestamp' => now()->toISOString()
                ], 10); // Cache for 10 seconds
            } else {
                Cache::forget($cacheKey);
            }

            // Broadcast typing event (would integrate with WebSocket)
            // event(new UserTyping($conversation, auth()->user(), $request->is_typing));

            return $this->successResponse(null, 'Typing status updated');

        } catch (\Exception $e) {
            return $this->handleException($e, 'updating_typing_status');
        }
    }

    /**
     * Get typing users
     */
    public function getTyping(Conversation $conversation): JsonResponse
    {
        try {
            if (!$conversation->participants()->where('user_id', auth()->id())->exists()) {
                return $this->forbiddenResponse('You are not a participant in this conversation');
            }

            $typingUsers = [];
            $participants = $conversation->participants()->pluck('user_id');

            foreach ($participants as $userId) {
                if ($userId === auth()->id()) continue;
                
                $cacheKey = "typing:{$conversation->id}:{$userId}";
                $typingData = Cache::get($cacheKey);
                
                if ($typingData) {
                    $typingUsers[] = $typingData;
                }
            }

            return $this->successResponse($typingUsers);

        } catch (\Exception $e) {
            return $this->handleException($e, 'getting_typing_users');
        }
    }

    /**
     * Update online status
     */
    public function updateOnlineStatus(Request $request, Conversation $conversation): JsonResponse
    {
        try {
            if (!$conversation->participants()->where('user_id', auth()->id())->exists()) {
                return $this->forbiddenResponse('You are not a participant in this conversation');
            }

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

            return $this->successResponse([
                'is_online' => $user->is_online,
                'last_seen_at' => $user->last_seen_at
            ]);

        } catch (\Exception $e) {
            return $this->handleException($e, 'updating_online_status');
        }
    }

    /**
     * Get conversation participants
     */
    public function getParticipants(Conversation $conversation): JsonResponse
    {
        try {
            if (!$conversation->participants()->where('user_id', auth()->id())->exists()) {
                return $this->forbiddenResponse('You are not a participant in this conversation');
            }

            $participants = $conversation->participants()
                ->select('users.id', 'users.name', 'users.email', 'users.avatar', 'users.last_seen_at', 'users.is_online')
                ->with('pivot')
                ->get()
                ->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'avatar' => $user->avatar,
                        'last_seen_at' => $user->last_seen_at,
                        'is_online' => $user->is_online,
                        'is_admin' => $user->pivot->is_admin,
                        'joined_at' => $user->pivot->joined_at,
                    ];
                });

            return $this->successResponse($participants);

        } catch (\Exception $e) {
            return $this->handleException($e, 'getting_participants');
        }
    }

    /**
     * Search conversations
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'query' => 'required|string|min:1|max:255',
                'per_page' => 'integer|min:1|max:50'
            ]);

            if ($validator->fails()) {
                return $this->validationErrorResponse($validator->errors());
            }

            $query = $request->query;
            $perPage = $request->get('per_page', 15);

            $conversations = Conversation::with([
                'participants:id,name,email,avatar',
                'lastMessage:id,content,type,sender_id,created_at,conversation_id'
            ])
            ->whereHas('participants', function ($q) {
                $q->where('user_id', auth()->id());
            })
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhereHas('participants', function ($participantQuery) use ($query) {
                      $participantQuery->where('name', 'like', "%{$query}%");
                  });
            })
            ->orderBy('updated_at', 'desc')
            ->paginate($perPage);

            $conversations->getCollection()->transform(function ($conversation) {
                return $this->transformConversation($conversation);
            });

            $this->logActivity('conversations_searched', ['query' => $query]);

            return $this->paginatedResponse($conversations, 'Search results retrieved successfully');

        } catch (\Exception $e) {
            return $this->handleException($e, 'searching_conversations');
        }
    }

    // Protected helper methods

    protected function updateParticipantStatus(Conversation $conversation, string $field, bool $value, string $action): JsonResponse
    {
        try {
            if (!$conversation->participants()->where('user_id', auth()->id())->exists()) {
                return $this->forbiddenResponse('You are not a participant in this conversation');
            }

            $conversation->participants()->updateExistingPivot(auth()->id(), [$field => $value]);

            $this->clearConversationCaches([auth()->id()]);
            $this->logActivity("conversation_{$action}", ['conversation_id' => $conversation->id]);

            return $this->successResponse(null, "Conversation {$action} successfully");

        } catch (\Exception $e) {
            return $this->handleException($e, "updating_conversation_status_{$action}");
        }
    }

    protected function transformConversation(Conversation $conversation): array
    {
        $otherParticipants = $conversation->participants->where('id', '!=', auth()->id());
        
        return [
            'id' => $conversation->id,
            'title' => $conversation->title,
            'type' => $conversation->type,
            'avatar' => $conversation->avatar,
            'description' => $conversation->description,
            'last_message' => $conversation->lastMessage ? [
                'id' => $conversation->lastMessage->id,
                'content' => $conversation->lastMessage->content,
                'type' => $conversation->lastMessage->type,
                'sender' => $conversation->lastMessage->sender ? [
                    'id' => $conversation->lastMessage->sender->id,
                    'name' => $conversation->lastMessage->sender->name,
                    'avatar' => $conversation->lastMessage->sender->avatar,
                ] : null,
                'created_at' => $conversation->lastMessage->created_at,
            ] : null,
            'participants' => $otherParticipants->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'avatar' => $user->avatar,
                    'is_online' => $user->is_online ?? false,
                ];
            })->values(),
            'unread_count' => $conversation->unread_messages_count ?? 0,
            'messages_count' => $conversation->messages_count ?? 0,
            'created_at' => $conversation->created_at,
            'updated_at' => $conversation->updated_at,
        ];
    }

    protected function transformConversationDetailed(Conversation $conversation): array
    {
        $basic = $this->transformConversation($conversation);
        
        $basic['messages'] = $conversation->messages->map(function ($message) {
            return [
                'id' => $message->id,
                'content' => $message->content,
                'type' => $message->type,
                'sender' => $message->sender ? [
                    'id' => $message->sender->id,
                    'name' => $message->sender->name,
                    'avatar' => $message->sender->avatar,
                ] : null,
                'reads' => $message->reads->pluck('user_id'),
                'reactions' => $message->reactions->groupBy('emoji')->map(function ($reactions) {
                    return [
                        'emoji' => $reactions->first()->emoji,
                        'count' => $reactions->count(),
                        'users' => $reactions->map(function ($reaction) {
                            return [
                                'id' => $reaction->user->id,
                                'name' => $reaction->user->name,
                            ];
                        })
                    ];
                })->values(),
                'created_at' => $message->created_at,
                'updated_at' => $message->updated_at,
            ];
        })->reverse()->values();

        return $basic;
    }

    protected function generateConversationTitle(array $participantIds): string
    {
        if (count($participantIds) <= 2) {
            return null; // Direct conversations don't need titles
        }

        $participants = User::whereIn('id', $participantIds)
            ->where('id', '!=', auth()->id())
            ->pluck('name')
            ->take(3);

        if ($participants->count() > 3) {
            return $participants->take(2)->join(', ') . ' and ' . ($participants->count() - 2) . ' others';
        }

        return $participants->join(', ');
    }

    protected function clearConversationCaches(array $userIds): void
    {
        foreach ($userIds as $userId) {
            Cache::tags(["user_conversations:{$userId}"])->flush();
        }
    }
}
