<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\MessageReaction;
use App\Models\TypingIndicator;
use App\Models\User;
use App\Models\UserOnlineStatus;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use App\Events\MessageSent;
use App\Events\TypingStarted;
use App\Events\TypingStopped;
use App\Events\MessageReactionAdded;
use App\Events\MessageRead;
use App\Events\UserTyping;
use App\Events\UserOnlineStatusChanged;

class MessagingController extends Controller
{
    /**
     * Display the messaging interface
     */
    public function index()
    {
        $user = Auth::user();
        
        // Update user's online status
        UserOnlineStatus::updateStatus($user->id, true);
        
        // Get user's conversations with unread counts
        $conversations = Conversation::forUser($user->id)
            ->with(['user1', 'user2', 'lastMessage.sender'])
            ->orderBy('last_message_at', 'desc')
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(function ($conversation) use ($user) {
                return [
                    'id' => $conversation->id,
                    'title' => $conversation->getDisplayNameForUser($user->id),
                    'avatar' => $conversation->getAvatarForUser($user->id),
                    'last_message' => $conversation->lastMessage ? [
                        'content' => $conversation->lastMessage->content,
                        'sender_name' => $conversation->lastMessage->sender->name,
                        'created_at' => $conversation->lastMessage->created_at->toISOString(),
                        'is_mine' => $conversation->lastMessage->sender_id === $user->id,
                    ] : null,
                    'unread_count' => $conversation->getUnreadCountForUser($user->id),
                    'other_user' => $conversation->otherParticipant($user->id),
                    'last_activity' => $conversation->last_message_at ? $conversation->last_message_at->toISOString() : $conversation->updated_at->toISOString(),
                ];
            });

        // Get online users for contacts
        $onlineUsers = UserOnlineStatus::online()
            ->with('user')
            ->whereHas('user', function ($query) use ($user) {
                $query->where('id', '!=', $user->id);
            })
            ->get()
            ->pluck('user')
            ->map(function ($onlineUser) {
                return [
                    'id' => $onlineUser->id,
                    'name' => $onlineUser->name,
                    'avatar' => $onlineUser->avatar ? asset('storage/' . $onlineUser->avatar) : asset('images/default-avatar.png'),
                    'status' => 'online',
                ];
            });

        return view('messages.index', compact('conversations', 'onlineUsers'));
    }

    /**
     * Get conversations for the authenticated user
     */
    public function getConversations(Request $request)
    {
        $user = Auth::user();
        
        $conversations = Conversation::forUser($user->id)
            ->with(['user1', 'user2', 'lastMessage.sender'])
            ->orderBy('last_message_at', 'desc')
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(function ($conversation) use ($user) {
                $otherUser = $conversation->otherParticipant($user->id);
                $isOnline = $otherUser ? UserOnlineStatus::isOnline($otherUser->id) : false;
                
                return [
                    'id' => $conversation->id,
                    'title' => $conversation->getDisplayNameForUser($user->id),
                    'avatar' => $conversation->getAvatarForUser($user->id),
                    'other_user_id' => $otherUser?->id,
                    'is_online' => $isOnline,
                    'last_message' => $conversation->lastMessage ? [
                        'id' => $conversation->lastMessage->id,
                        'content' => $conversation->lastMessage->content,
                        'message_type' => $conversation->lastMessage->message_type,
                        'sender_name' => $conversation->lastMessage->sender->name,
                        'sender_id' => $conversation->lastMessage->sender_id,
                        'created_at' => $conversation->lastMessage->created_at->toISOString(),
                        'is_mine' => $conversation->lastMessage->sender_id === $user->id,
                    ] : null,
                    'unread_count' => $conversation->getUnreadCountForUser($user->id),
                    'last_activity' => $conversation->last_message_at ? $conversation->last_message_at->toISOString() : $conversation->updated_at->toISOString(),
                ];
            });

        return response()->json(['conversations' => $conversations]);
    }

    /**
     * Get messages for a specific conversation
     */
    public function getMessages(Request $request, $conversationId)
    {
        $user = Auth::user();
        $conversation = Conversation::findOrFail($conversationId);

        // Check if user is participant
        if (!$conversation->hasParticipant($user->id)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $page = $request->get('page', 1);
        $perPage = $request->get('per_page', 50);

        $messages = Message::where('conversation_id', $conversationId)
            ->with(['sender', 'replyTo.sender'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        $formattedMessages = $messages->getCollection()->map(function ($message) use ($user) {
            return [
                'id' => $message->id,
                'conversation_id' => $message->conversation_id,
                'content' => $message->content,
                'message_type' => $message->message_type,
                'file_url' => $message->file_url,
                'file_name' => $message->file_name,
                'file_size' => $message->file_size,
                'formatted_file_size' => $message->formatted_file_size,
                'sender_id' => $message->sender_id,
                'sender_name' => $message->sender->name,
                'sender_avatar' => $message->sender->avatar ? asset('storage/' . $message->sender->avatar) : asset('images/default-avatar.png'),
                'is_mine' => $message->sender_id === $user->id,
                'is_read' => $message->is_read,
                'read_by' => $message->read_by,
                'is_read_by_me' => $message->isReadBy($user->id),
                'reply_to' => $message->replyTo ? [
                    'id' => $message->replyTo->id,
                    'content' => $message->replyTo->content,
                    'sender_name' => $message->replyTo->sender->name,
                ] : null,
                'reactions' => $message->reactions,
                'metadata' => $message->metadata,
                'created_at' => $message->created_at->toISOString(),
                'updated_at' => $message->updated_at->toISOString(),
                'edited_at' => $message->edited_at?->toISOString(),
            ];
        })->reverse()->values();

        // Mark messages as read
        $conversation->markAsReadForUser($user->id);

        return response()->json([
            'messages' => $formattedMessages,
            'pagination' => [
                'current_page' => $messages->currentPage(),
                'last_page' => $messages->lastPage(),
                'per_page' => $messages->perPage(),
                'total' => $messages->total(),
                'has_more' => $messages->hasMorePages(),
            ]
        ]);
    }

    /**
     * Send a new message
     */
    public function sendMessage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'conversation_id' => 'nullable|exists:conversations,id',
            'recipient_id' => 'nullable|exists:users,id',
            'content' => 'required_without:file|string',
            'message_type' => 'in:text,image,video,audio,file,call',
            'file' => 'nullable|file|max:50000', // 50MB max
            'reply_to_id' => 'nullable|exists:messages,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = Auth::user();
        $messageType = $request->get('message_type', 'text');

        // Handle conversation creation or retrieval
        if ($request->conversation_id) {
            $conversation = Conversation::findOrFail($request->conversation_id);
            if (!$conversation->hasParticipant($user->id)) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
        } else if ($request->recipient_id) {
            $conversation = Conversation::findOrCreateBetweenUsers($user->id, $request->recipient_id);
        } else {
            return response()->json(['error' => 'Either conversation_id or recipient_id is required'], 422);
        }

        // Handle file upload
        $fileUrl = null;
        $fileName = null;
        $fileSize = null;

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = $file->getClientOriginalName();
            $fileSize = $file->getSize();
            
            // Determine message type based on file if not specified
            if ($messageType === 'text') {
                $mimeType = $file->getMimeType();
                if (str_starts_with($mimeType, 'image/')) {
                    $messageType = 'image';
                } elseif (str_starts_with($mimeType, 'video/')) {
                    $messageType = 'video';
                } elseif (str_starts_with($mimeType, 'audio/')) {
                    $messageType = 'audio';
                } else {
                    $messageType = 'file';
                }
            }

            $path = $file->store('messages/' . $conversation->id, 'public');
            $fileUrl = Storage::url($path);
        }

        // Create the message
        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $user->id,
            'content' => $request->content,
            'message_type' => $messageType,
            'file_url' => $fileUrl,
            'file_name' => $fileName,
            'file_size' => $fileSize,
            'reply_to_id' => $request->reply_to_id,
            'read_by' => [$user->id], // Mark as read by sender
        ]);

        // Update conversation
        $conversation->updateLastMessage($message);

        // Stop typing indicator
        TypingIndicator::stopTyping($conversation->id, $user->id);

        // Broadcast the message
        broadcast(new MessageSent($message, $user));

        return response()->json([
            'message' => [
                'id' => $message->id,
                'conversation_id' => $message->conversation_id,
                'content' => $message->content,
                'message_type' => $message->message_type,
                'file_url' => $message->file_url,
                'file_name' => $message->file_name,
                'file_size' => $message->file_size,
                'formatted_file_size' => $message->formatted_file_size,
                'sender_id' => $message->sender_id,
                'sender_name' => $user->name,
                'sender_avatar' => $user->avatar ? asset('storage/' . $user->avatar) : asset('images/default-avatar.png'),
                'is_mine' => true,
                'is_read' => $message->is_read,
                'read_by' => $message->read_by,
                'reply_to' => null,
                'reactions' => $message->reactions,
                'metadata' => $message->metadata,
                'created_at' => $message->created_at->toISOString(),
                'updated_at' => $message->updated_at->toISOString(),
            ]
        ], 201);
    }

    /**
     * Mark message as read
     */
    public function markAsRead(Request $request, $messageId)
    {
        $user = Auth::user();
        $message = Message::findOrFail($messageId);
        
        // Check if user is participant in the conversation
        if (!$message->conversation->hasParticipant($user->id)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Don't mark own messages as read
        if ($message->sender_id === $user->id) {
            return response()->json(['success' => true]);
        }

        $message->markAsReadBy($user->id);

        // Broadcast read status
        broadcast(new MessageRead($message, $user));

        return response()->json(['success' => true]);
    }

    /**
     * Update typing status
     */
    public function updateTyping(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'conversation_id' => 'required|exists:conversations,id',
            'is_typing' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = Auth::user();
        $conversationId = $request->conversation_id;
        $isTyping = $request->is_typing;

        $conversation = Conversation::findOrFail($conversationId);
        if (!$conversation->hasParticipant($user->id)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($isTyping) {
            TypingIndicator::startTyping($conversationId, $user->id);
        } else {
            TypingIndicator::stopTyping($conversationId, $user->id);
        }

        // Broadcast typing status
        broadcast(new UserTyping($user, $conversationId, $isTyping));

        return response()->json(['success' => true]);
    }

    /**
     * Get typing indicators for a conversation
     */
    public function getTypingIndicators(Request $request, $conversationId)
    {
        $user = Auth::user();
        $conversation = Conversation::findOrFail($conversationId);

        if (!$conversation->hasParticipant($user->id)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $typingIndicators = TypingIndicator::getActiveTypingInConversation($conversationId, $user->id);

        $typing = $typingIndicators->map(function ($indicator) {
            return [
                'user_id' => $indicator->user_id,
                'user_name' => $indicator->user->name,
                'started_at' => $indicator->started_at->toISOString(),
            ];
        });

        return response()->json(['typing' => $typing]);
    }

    /**
     * Update user online status
     */
    public function updateOnlineStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'is_online' => 'required|boolean',
            'status_message' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = Auth::user();
        $isOnline = $request->is_online;

        UserOnlineStatus::updateStatus(
            $user->id,
            $isOnline,
            $request->status_message,
            [
                'user_agent' => $request->userAgent(),
                'ip_address' => $request->ip(),
            ]
        );

        // Update user's last_seen_at
        $user->update(['last_seen_at' => now()]);

        // Broadcast status change
        broadcast(new UserOnlineStatusChanged($user, $isOnline, now()));

        return response()->json(['success' => true]);
    }

    /**
     * Get online users
     */
    public function getOnlineUsers(Request $request)
    {
        $user = Auth::user();
        
        $onlineUsers = UserOnlineStatus::online()
            ->with('user')
            ->whereHas('user', function ($query) use ($user) {
                $query->where('id', '!=', $user->id);
            })
            ->get()
            ->map(function ($status) {
                return [
                    'id' => $status->user->id,
                    'name' => $status->user->name,
                    'avatar' => $status->user->avatar ? asset('storage/' . $status->user->avatar) : asset('images/default-avatar.png'),
                    'status_message' => $status->status_message,
                    'last_seen_at' => $status->last_seen_at->toISOString(),
                ];
            });

        return response()->json(['online_users' => $onlineUsers]);
    }

    /**
     * Search users for starting new conversations
     */
    public function searchUsers(Request $request)
    {
        $query = $request->get('q', '');
        $user = Auth::user();

        if (strlen($query) < 2) {
            return response()->json(['users' => []]);
        }

        $users = User::where('id', '!=', $user->id)
            ->where(function ($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('email', 'LIKE', "%{$query}%")
                  ->orWhere('username', 'LIKE', "%{$query}%");
            })
            ->limit(20)
            ->get()
            ->map(function ($foundUser) {
                return [
                    'id' => $foundUser->id,
                    'name' => $foundUser->name,
                    'email' => $foundUser->email,
                    'username' => $foundUser->username,
                    'avatar' => $foundUser->avatar ? asset('storage/' . $foundUser->avatar) : asset('images/default-avatar.png'),
                    'is_online' => UserOnlineStatus::isOnline($foundUser->id),
                ];
            });

        return response()->json(['users' => $users]);
    }

    /**
     * Add reaction to a message
     */
    public function addReaction(Request $request, $messageId)
    {
        $validator = Validator::make($request->all(), [
            'emoji' => 'required|string|size:2', // Assuming single emoji
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = Auth::user();
        $message = Message::findOrFail($messageId);

        if (!$message->conversation->hasParticipant($user->id)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $message->addReaction($request->emoji, $user->id);

        // You could broadcast this if needed
        // broadcast(new MessageReactionAdded($message, $user, $request->emoji));

        return response()->json([
            'success' => true,
            'reactions' => $message->fresh()->reactions
        ]);
    }

    /**
     * Clean up expired typing indicators (called by scheduled task)
     */
    public function cleanupTypingIndicators()
    {
        TypingIndicator::cleanupExpired();
        return response()->json(['success' => true]);
    }
}
