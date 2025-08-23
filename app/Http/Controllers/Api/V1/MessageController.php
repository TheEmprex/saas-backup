<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\BaseController;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\MessageRead;
use App\Models\MessageReaction;
use App\Services\LoggingService;
use App\Services\CachingService;
use App\Events\MessageSent;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MessageController extends BaseController
{
    protected CachingService $cache;

    public function __construct(LoggingService $logger, CachingService $cache)
    {
        parent::__construct($logger);
        $this->cache = $cache;
    }

    /**
     * Get messages for a conversation with pagination
     */
    public function index(Request $request, Conversation $conversation): JsonResponse
    {
        try {
            // Check if user is participant
            if (!$conversation->participants()->where('user_id', auth()->id())->exists()) {
                return $this->forbiddenResponse('You are not a participant in this conversation');
            }

            $validator = Validator::make($request->all(), [
                'per_page' => 'integer|min:1|max:100',
                'page' => 'integer|min:1',
                'before' => 'integer|exists:messages,id',
                'after' => 'integer|exists:messages,id'
            ]);

            if ($validator->fails()) {
                return $this->validationErrorResponse($validator->errors());
            }

            $perPage = $request->get('per_page', 50);
            
            $query = $conversation->messages()
                ->with([
                    'sender:id,name,avatar',
                    'reads' => function ($q) {
                        $q->select('message_id', 'user_id', 'read_at');
                    },
                    'reactions.user:id,name',
                    'repliedToMessage:id,content,type,sender_id',
                    'repliedToMessage.sender:id,name'
                ])
                ->select('id', 'conversation_id', 'sender_id', 'content', 'type', 'file_path', 'file_name', 'file_size', 'reply_to_id', 'created_at', 'updated_at', 'edited_at');

            // Handle cursor-based pagination
            if ($request->has('before')) {
                $query->where('id', '<', $request->before);
            } elseif ($request->has('after')) {
                $query->where('id', '>', $request->after);
            }

            $messages = $query->latest()->paginate($perPage);

            // Transform messages for response
            $messages->getCollection()->transform(function ($message) {
                return $this->transformMessage($message);
            });

            $this->logActivity('messages_viewed', [
                'conversation_id' => $conversation->id,
                'count' => $messages->count()
            ]);

            return $this->paginatedResponse($messages, 'Messages retrieved successfully');

        } catch (\Exception $e) {
            return $this->handleException($e, 'getting_messages');
        }
    }

    /**
     * Send a new message
     */
    public function store(Request $request, Conversation $conversation): JsonResponse
    {
        try {
            // Check if user is participant
            if (!$conversation->participants()->where('user_id', auth()->id())->exists()) {
                return $this->forbiddenResponse('You are not a participant in this conversation');
            }

            $validator = Validator::make($request->all(), [
                'content' => 'nullable|string|max:10000',
                'type' => 'required|in:text,image,file,voice,system',
                'reply_to_id' => 'nullable|exists:messages,id',
                'file' => 'nullable|file|max:50000', // 50MB max
                'metadata' => 'nullable|array'
            ]);

            if ($validator->fails()) {
                return $this->validationErrorResponse($validator->errors());
            }

            // Validate content based on type
            if ($request->type === 'text' && empty($request->content)) {
                return $this->errorResponse('Content is required for text messages', 422);
            }

            if (in_array($request->type, ['image', 'file', 'voice']) && !$request->hasFile('file')) {
                return $this->errorResponse('File is required for this message type', 422);
            }

            DB::beginTransaction();

            $messageData = [
                'conversation_id' => $conversation->id,
                'sender_id' => auth()->id(),
                'content' => $request->content,
                'type' => $request->type,
                'reply_to_id' => $request->reply_to_id,
                'metadata' => $request->metadata ? json_encode($request->metadata) : null
            ];

            // Handle file upload
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $fileName = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs('messages', $fileName, 'public');

                $messageData['file_path'] = $filePath;
                $messageData['file_name'] = $file->getClientOriginalName();
                $messageData['file_size'] = $file->getSize();
                $messageData['file_type'] = $file->getMimeType();
            }

            $message = Message::create($messageData);

            // Update conversation timestamp
            $conversation->touch();

            // Mark message as read by sender
            MessageRead::create([
                'message_id' => $message->id,
                'user_id' => auth()->id(),
                'read_at' => now()
            ]);

            DB::commit();

            // Load relationships
            $message->load([
                'sender:id,name,avatar',
                'reads',
                'reactions.user:id,name',
                'repliedToMessage:id,content,type,sender_id',
                'repliedToMessage.sender:id,name'
            ]);

            // Clear conversation caches
            $this->clearMessageCaches($conversation);

            // Broadcast message event for real-time updates
            event(new MessageSent($message, auth()->user()));

            $this->logActivity('message_sent', [
                'message_id' => $message->id,
                'conversation_id' => $conversation->id,
                'type' => $message->type,
                'has_file' => !empty($message->file_path)
            ]);

            $this->logAudit('message_sent', $message);

            return $this->createdResponse(
                $this->transformMessage($message),
                'Message sent successfully'
            );

        } catch (\Exception $e) {
            DB::rollback();
            return $this->handleException($e, 'sending_message');
        }
    }

    /**
     * Get a specific message
     */
    public function show(Message $message): JsonResponse
    {
        try {
            // Check if user has access to this message
            if (!$message->conversation->participants()->where('user_id', auth()->id())->exists()) {
                return $this->forbiddenResponse('You do not have access to this message');
            }

            $message->load([
                'sender:id,name,avatar',
                'reads',
                'reactions.user:id,name',
                'repliedToMessage:id,content,type,sender_id',
                'repliedToMessage.sender:id,name'
            ]);

            return $this->successResponse(
                $this->transformMessage($message),
                'Message retrieved successfully'
            );

        } catch (\Exception $e) {
            return $this->handleException($e, 'getting_message');
        }
    }

    /**
     * Update a message (edit)
     */
    public function update(Request $request, Message $message): JsonResponse
    {
        try {
            // Only sender can edit their own messages
            if ($message->sender_id !== auth()->id()) {
                return $this->forbiddenResponse('You can only edit your own messages');
            }

            // Can't edit system messages
            if ($message->type === 'system') {
                return $this->forbiddenResponse('System messages cannot be edited');
            }

            // Can't edit messages older than 24 hours
            if ($message->created_at->lt(now()->subHours(24))) {
                return $this->forbiddenResponse('Messages older than 24 hours cannot be edited');
            }

            $validator = Validator::make($request->all(), [
                'content' => 'required|string|max:10000'
            ]);

            if ($validator->fails()) {
                return $this->validationErrorResponse($validator->errors());
            }

            $oldContent = $message->content;
            $message->update([
                'content' => $request->content,
                'edited_at' => now()
            ]);

            $this->logActivity('message_edited', [
                'message_id' => $message->id,
                'conversation_id' => $message->conversation_id
            ]);

            $this->logAudit('message_edited', $message, [
                'old_content' => $oldContent,
                'new_content' => $request->content
            ]);

            return $this->successResponse(
                $this->transformMessage($message),
                'Message updated successfully'
            );

        } catch (\Exception $e) {
            return $this->handleException($e, 'updating_message');
        }
    }

    /**
     * Delete a message
     */
    public function destroy(Message $message): JsonResponse
    {
        try {
            // Only sender can delete their own messages
            if ($message->sender_id !== auth()->id()) {
                return $this->forbiddenResponse('You can only delete your own messages');
            }

            DB::beginTransaction();

            // Delete associated files
            if ($message->file_path) {
                Storage::disk('public')->delete($message->file_path);
            }

            // Delete reads and reactions
            $message->reads()->delete();
            $message->reactions()->delete();

            // Soft delete the message
            $message->delete();

            DB::commit();

            $this->clearMessageCaches($message->conversation);

            $this->logActivity('message_deleted', [
                'message_id' => $message->id,
                'conversation_id' => $message->conversation_id
            ]);

            return $this->deletedResponse('Message deleted successfully');

        } catch (\Exception $e) {
            DB::rollback();
            return $this->handleException($e, 'deleting_message');
        }
    }

    /**
     * Mark messages as read
     */
    public function markAsRead(Request $request, Conversation $conversation): JsonResponse
    {
        try {
            // Check if user is participant
            if (!$conversation->participants()->where('user_id', auth()->id())->exists()) {
                return $this->forbiddenResponse('You are not a participant in this conversation');
            }

            $validator = Validator::make($request->all(), [
                'message_ids' => 'nullable|array',
                'message_ids.*' => 'exists:messages,id',
                'mark_all' => 'nullable|boolean'
            ]);

            if ($validator->fails()) {
                return $this->validationErrorResponse($validator->errors());
            }

            DB::beginTransaction();

            if ($request->mark_all || empty($request->message_ids)) {
                // Mark all unread messages as read
                $unreadMessages = $conversation->messages()
                    ->whereDoesntHave('reads', function ($q) {
                        $q->where('user_id', auth()->id());
                    })
                    ->pluck('id');

                foreach ($unreadMessages as $messageId) {
                    MessageRead::firstOrCreate([
                        'message_id' => $messageId,
                        'user_id' => auth()->id()
                    ], [
                        'read_at' => now()
                    ]);
                }

                $markedCount = $unreadMessages->count();
            } else {
                // Mark specific messages as read
                $markedCount = 0;
                foreach ($request->message_ids as $messageId) {
                    $read = MessageRead::firstOrCreate([
                        'message_id' => $messageId,
                        'user_id' => auth()->id()
                    ], [
                        'read_at' => now()
                    ]);

                    if ($read->wasRecentlyCreated) {
                        $markedCount++;
                    }
                }
            }

            DB::commit();

            $this->logActivity('messages_marked_read', [
                'conversation_id' => $conversation->id,
                'count' => $markedCount
            ]);

            return $this->successResponse([
                'marked_count' => $markedCount
            ], "Marked {$markedCount} messages as read");

        } catch (\Exception $e) {
            DB::rollback();
            return $this->handleException($e, 'marking_messages_read');
        }
    }

    /**
     * Add reaction to message
     */
    public function addReaction(Request $request, Message $message): JsonResponse
    {
        try {
            // Check if user has access to this message
            if (!$message->conversation->participants()->where('user_id', auth()->id())->exists()) {
                return $this->forbiddenResponse('You do not have access to this message');
            }

            $validator = Validator::make($request->all(), [
                'emoji' => 'required|string|max:10'
            ]);

            if ($validator->fails()) {
                return $this->validationErrorResponse($validator->errors());
            }

            // Toggle reaction (add if not exists, remove if exists)
            $existingReaction = MessageReaction::where([
                'message_id' => $message->id,
                'user_id' => auth()->id(),
                'emoji' => $request->emoji
            ])->first();

            if ($existingReaction) {
                $existingReaction->delete();
                $action = 'removed';
            } else {
                MessageReaction::create([
                    'message_id' => $message->id,
                    'user_id' => auth()->id(),
                    'emoji' => $request->emoji
                ]);
                $action = 'added';
            }

            // Get updated reactions
            $reactions = $message->reactions()
                ->with('user:id,name')
                ->get()
                ->groupBy('emoji')
                ->map(function ($reactions) {
                    return [
                        'emoji' => $reactions->first()->emoji,
                        'count' => $reactions->count(),
                        'users' => $reactions->map(function ($reaction) {
                            return [
                                'id' => $reaction->user->id,
                                'name' => $reaction->user->name
                            ];
                        })
                    ];
                })
                ->values();

            $this->logActivity('message_reaction_' . $action, [
                'message_id' => $message->id,
                'emoji' => $request->emoji
            ]);

            return $this->successResponse([
                'reactions' => $reactions,
                'action' => $action
            ], "Reaction {$action} successfully");

        } catch (\Exception $e) {
            return $this->handleException($e, 'adding_reaction');
        }
    }

    /**
     * Remove reaction from message
     */
    public function removeReaction(Message $message, MessageReaction $reaction): JsonResponse
    {
        try {
            // Check if user owns this reaction
            if ($reaction->user_id !== auth()->id()) {
                return $this->forbiddenResponse('You can only remove your own reactions');
            }

            // Check if reaction belongs to this message
            if ($reaction->message_id !== $message->id) {
                return $this->forbiddenResponse('Reaction does not belong to this message');
            }

            $reaction->delete();

            $this->logActivity('message_reaction_removed', [
                'message_id' => $message->id,
                'reaction_id' => $reaction->id
            ]);

            return $this->successResponse(null, 'Reaction removed successfully');

        } catch (\Exception $e) {
            return $this->handleException($e, 'removing_reaction');
        }
    }

    /**
     * Reply to a message
     */
    public function reply(Request $request, Message $message): JsonResponse
    {
        try {
            // Check if user has access to this message
            if (!$message->conversation->participants()->where('user_id', auth()->id())->exists()) {
                return $this->forbiddenResponse('You do not have access to this message');
            }

            $validator = Validator::make($request->all(), [
                'content' => 'required|string|max:10000',
                'type' => 'in:text,image,file,voice'
            ]);

            if ($validator->fails()) {
                return $this->validationErrorResponse($validator->errors());
            }

            $replyMessage = Message::create([
                'conversation_id' => $message->conversation_id,
                'sender_id' => auth()->id(),
                'content' => $request->content,
                'type' => $request->get('type', 'text'),
                'reply_to_id' => $message->id
            ]);

            // Update conversation timestamp
            $message->conversation->touch();

            $replyMessage->load([
                'sender:id,name,avatar',
                'repliedToMessage:id,content,type,sender_id',
                'repliedToMessage.sender:id,name'
            ]);

            $this->logActivity('message_replied', [
                'reply_id' => $replyMessage->id,
                'original_message_id' => $message->id
            ]);

            return $this->createdResponse(
                $this->transformMessage($replyMessage),
                'Reply sent successfully'
            );

        } catch (\Exception $e) {
            return $this->handleException($e, 'replying_to_message');
        }
    }

    /**
     * Forward a message
     */
    public function forward(Request $request, Message $message): JsonResponse
    {
        try {
            // Check if user has access to this message
            if (!$message->conversation->participants()->where('user_id', auth()->id())->exists()) {
                return $this->forbiddenResponse('You do not have access to this message');
            }

            $validator = Validator::make($request->all(), [
                'conversation_ids' => 'required|array|min:1',
                'conversation_ids.*' => 'exists:conversations,id'
            ]);

            if ($validator->fails()) {
                return $this->validationErrorResponse($validator->errors());
            }

            $forwardedMessages = [];

            foreach ($request->conversation_ids as $conversationId) {
                $conversation = Conversation::find($conversationId);
                
                // Check if user is participant in target conversation
                if (!$conversation->participants()->where('user_id', auth()->id())->exists()) {
                    continue; // Skip conversations user is not part of
                }

                $forwardedMessage = Message::create([
                    'conversation_id' => $conversationId,
                    'sender_id' => auth()->id(),
                    'content' => $message->content,
                    'type' => $message->type,
                    'file_path' => $message->file_path,
                    'file_name' => $message->file_name,
                    'file_size' => $message->file_size,
                    'file_type' => $message->file_type,
                    'metadata' => json_encode(['forwarded_from' => $message->id])
                ]);

                $conversation->touch();
                $forwardedMessages[] = $forwardedMessage;
            }

            $this->logActivity('message_forwarded', [
                'original_message_id' => $message->id,
                'forwarded_count' => count($forwardedMessages)
            ]);

            return $this->successResponse([
                'forwarded_count' => count($forwardedMessages),
                'forwarded_to' => $request->conversation_ids
            ], 'Message forwarded successfully');

        } catch (\Exception $e) {
            return $this->handleException($e, 'forwarding_message');
        }
    }

    /**
     * Upload file for message
     */
    public function uploadFile(Request $request, Conversation $conversation): JsonResponse
    {
        try {
            // Check if user is participant
            if (!$conversation->participants()->where('user_id', auth()->id())->exists()) {
                return $this->forbiddenResponse('You are not a participant in this conversation');
            }

            $validator = Validator::make($request->all(), [
                'file' => 'required|file|max:50000', // 50MB max
                'type' => 'required|in:file,document,archive'
            ]);

            if ($validator->fails()) {
                return $this->validationErrorResponse($validator->errors());
            }

            return $this->handleFileUpload($request, $conversation, 'file');

        } catch (\Exception $e) {
            return $this->handleException($e, 'uploading_file');
        }
    }

    /**
     * Upload image for message
     */
    public function uploadImage(Request $request, Conversation $conversation): JsonResponse
    {
        try {
            // Check if user is participant
            if (!$conversation->participants()->where('user_id', auth()->id())->exists()) {
                return $this->forbiddenResponse('You are not a participant in this conversation');
            }

            $validator = Validator::make($request->all(), [
                'file' => 'required|image|max:10000', // 10MB max for images
                'content' => 'nullable|string|max:1000'
            ]);

            if ($validator->fails()) {
                return $this->validationErrorResponse($validator->errors());
            }

            return $this->handleFileUpload($request, $conversation, 'image', $request->content);

        } catch (\Exception $e) {
            return $this->handleException($e, 'uploading_image');
        }
    }

    /**
     * Upload voice message
     */
    public function uploadVoice(Request $request, Conversation $conversation): JsonResponse
    {
        try {
            // Check if user is participant
            if (!$conversation->participants()->where('user_id', auth()->id())->exists()) {
                return $this->forbiddenResponse('You are not a participant in this conversation');
            }

            $validator = Validator::make($request->all(), [
                'file' => 'required|file|mimes:mp3,wav,m4a,ogg|max:5000', // 5MB max for voice
                'duration' => 'nullable|integer|min:1|max:600' // Max 10 minutes
            ]);

            if ($validator->fails()) {
                return $this->validationErrorResponse($validator->errors());
            }

            $metadata = [];
            if ($request->duration) {
                $metadata['duration'] = $request->duration;
            }

            return $this->handleFileUpload($request, $conversation, 'voice', null, $metadata);

        } catch (\Exception $e) {
            return $this->handleException($e, 'uploading_voice');
        }
    }

    /**
     * Search messages
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'query' => 'required|string|min:1|max:255',
                'conversation_id' => 'nullable|exists:conversations,id',
                'per_page' => 'integer|min:1|max:50',
                'type' => 'nullable|in:text,image,file,voice,all'
            ]);

            if ($validator->fails()) {
                return $this->validationErrorResponse($validator->errors());
            }

            $query = $request->query;
            $perPage = $request->get('per_page', 15);
            $type = $request->get('type', 'all');

            $messageQuery = Message::with([
                'sender:id,name,avatar',
                'conversation:id,title,type'
            ])
            ->whereHas('conversation.participants', function ($q) {
                $q->where('user_id', auth()->id());
            })
            ->where('content', 'like', "%{$query}%");

            if ($request->conversation_id) {
                $messageQuery->where('conversation_id', $request->conversation_id);
            }

            if ($type !== 'all') {
                $messageQuery->where('type', $type);
            }

            $messages = $messageQuery->orderBy('created_at', 'desc')->paginate($perPage);

            $messages->getCollection()->transform(function ($message) {
                return $this->transformMessage($message, true);
            });

            $this->logActivity('messages_searched', [
                'query' => $query,
                'type' => $type,
                'conversation_id' => $request->conversation_id
            ]);

            return $this->paginatedResponse($messages, 'Search results retrieved successfully');

        } catch (\Exception $e) {
            return $this->handleException($e, 'searching_messages');
        }
    }

    // Protected helper methods

    protected function handleFileUpload(Request $request, Conversation $conversation, string $type, ?string $content = null, array $metadata = []): JsonResponse
    {
        DB::beginTransaction();

        try {
            $file = $request->file('file');
            $fileName = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs("messages/{$type}s", $fileName, 'public');

            $message = Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => auth()->id(),
                'content' => $content,
                'type' => $type,
                'file_path' => $filePath,
                'file_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'file_type' => $file->getMimeType(),
                'metadata' => !empty($metadata) ? json_encode($metadata) : null
            ]);

            // Update conversation timestamp
            $conversation->touch();

            // Mark as read by sender
            MessageRead::create([
                'message_id' => $message->id,
                'user_id' => auth()->id(),
                'read_at' => now()
            ]);

            DB::commit();

            $message->load(['sender:id,name,avatar']);

            $this->logActivity('file_uploaded', [
                'message_id' => $message->id,
                'file_type' => $type,
                'file_size' => $file->getSize()
            ]);

            return $this->createdResponse(
                $this->transformMessage($message),
                ucfirst($type) . ' uploaded successfully'
            );

        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    protected function transformMessage(Message $message, bool $includeConversation = false): array
    {
        $data = [
            'id' => $message->id,
            'content' => $message->content,
            'type' => $message->type,
            'sender' => $message->sender ? [
                'id' => $message->sender->id,
                'name' => $message->sender->name,
                'avatar' => $message->sender->avatar,
            ] : null,
            'file' => $message->file_path ? [
                'path' => Storage::url($message->file_path),
                'name' => $message->file_name,
                'size' => $message->file_size,
                'type' => $message->file_type,
            ] : null,
            'reply_to' => $message->repliedToMessage ? [
                'id' => $message->repliedToMessage->id,
                'content' => $message->repliedToMessage->content,
                'type' => $message->repliedToMessage->type,
                'sender' => $message->repliedToMessage->sender ? [
                    'id' => $message->repliedToMessage->sender->id,
                    'name' => $message->repliedToMessage->sender->name,
                ] : null,
            ] : null,
            'reads' => $message->reads ? $message->reads->map(function ($read) {
                return [
                    'user_id' => $read->user_id,
                    'read_at' => $read->read_at,
                ];
            }) : [],
            'reactions' => $message->reactions ? $message->reactions->groupBy('emoji')->map(function ($reactions) {
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
            })->values() : [],
            'metadata' => $message->metadata ? json_decode($message->metadata, true) : null,
            'created_at' => $message->created_at,
            'updated_at' => $message->updated_at,
            'edited_at' => $message->edited_at,
        ];

        if ($includeConversation && $message->conversation) {
            $data['conversation'] = [
                'id' => $message->conversation->id,
                'title' => $message->conversation->title,
                'type' => $message->conversation->type,
            ];
        }

        return $data;
    }

    protected function clearMessageCaches(Conversation $conversation): void
    {
        $participantIds = $conversation->participants()->pluck('user_id');
        
        foreach ($participantIds as $userId) {
            Cache::tags(["user_conversations:{$userId}"])->flush();
        }
    }
}
