<?php

namespace App\Services;

use App\Events\MessageRead;
use App\Events\MessageSent;
use App\Events\UserOnlineStatusChanged;
use App\Events\UserTyping;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\TypingIndicator;
use App\Models\User;
use App\Models\UserOnlineStatus;
use App\DTOs\MessageData;
use App\DTOs\ConversationData;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class MessagingService
{
    /**
     * Get conversations for a specific user with eager loading
     */
    public function getConversationsForUser(int $userId): Collection
    {
        return Conversation::forUserOptimized($userId)
            ->active()
            ->get()
            ->map(function ($conversation) use ($userId) {
                return $this->formatConversationForUser($conversation, $userId);
            });
    }

    /**
     * Get paginated messages for a conversation
     */
    public function getMessages(int $conversationId, int $userId, int $page = 1, int $perPage = 50): array
    {
        $conversation = Conversation::findOrFail($conversationId);
        
        if (!$conversation->hasParticipant($userId)) {
            throw new \Exception('Unauthorized access to conversation');
        }

        $messages = Message::where('conversation_id', $conversationId)
            ->with([
                'sender:id,name,avatar',
                'replyTo:id,content,sender_id',
                'replyTo.sender:id,name'
            ])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        $formattedMessages = $messages->getCollection()
            ->map(fn($message) => $this->formatMessage($message, $userId))
            ->reverse()
            ->values();

        // Mark messages as read
        $this->markConversationAsReadForUser($conversation, $userId);

        return [
            'messages' => $formattedMessages,
            'pagination' => [
                'current_page' => $messages->currentPage(),
                'last_page' => $messages->lastPage(),
                'per_page' => $messages->perPage(),
                'total' => $messages->total(),
                'has_more' => $messages->hasMorePages(),
            ]
        ];
    }

    /**
     * Send a new message
     */
    public function sendMessage(MessageData $messageData): Message
    {
        $conversation = $this->getOrCreateConversation($messageData);
        
        if (!$conversation->hasParticipant($messageData->senderId)) {
            throw new \Exception('Unauthorized access to conversation');
        }

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $messageData->senderId,
            'content' => $messageData->content,
            'message_type' => $messageData->messageType,
            'file_url' => $messageData->fileUrl,
            'file_name' => $messageData->fileName,
            'file_size' => $messageData->fileSize,
            'reply_to_id' => $messageData->replyToId,
            'read_by' => [$messageData->senderId], // Mark as read by sender
        ]);

        // Update conversation
        $conversation->updateLastMessage($message);

        // Stop typing indicator
        TypingIndicator::stopTyping($conversation->id, $messageData->senderId);

        // Load relationships for broadcasting
        $message->load('sender:id,name,avatar');
        $user = User::find($messageData->senderId);

        // Broadcast the message
        broadcast(new MessageSent($message, $user));

        return $message;
    }

    /**
     * Handle file upload for messages
     */
    public function handleFileUpload(UploadedFile $file, int $conversationId): array
    {
        $fileName = $file->getClientOriginalName();
        $fileSize = $file->getSize();
        
        // Determine message type based on file
        $messageType = $this->determineMessageTypeFromFile($file);
        
        $path = $file->store('messages/' . $conversationId, 'public');
        $fileUrl = Storage::url($path);

        return [
            'file_url' => $fileUrl,
            'file_name' => $fileName,
            'file_size' => $fileSize,
            'message_type' => $messageType
        ];
    }

    /**
     * Mark message as read by user
     */
    public function markMessageAsRead(int $messageId, int $userId): void
    {
        $message = Message::findOrFail($messageId);
        
        if (!$message->conversation->hasParticipant($userId)) {
            throw new \Exception('Unauthorized access to message');
        }

        // Don't mark own messages as read
        if ($message->sender_id === $userId) {
            return;
        }

        $message->markAsReadBy($userId);
        
        $user = User::find($userId);
        broadcast(new MessageRead($message, $user));
    }

    /**
     * Update typing status
     */
    public function updateTypingStatus(int $conversationId, int $userId, bool $isTyping): void
    {
        $conversation = Conversation::findOrFail($conversationId);
        
        if (!$conversation->hasParticipant($userId)) {
            throw new \Exception('Unauthorized access to conversation');
        }

        if ($isTyping) {
            TypingIndicator::startTyping($conversationId, $userId);
        } else {
            TypingIndicator::stopTyping($conversationId, $userId);
        }

        $user = User::find($userId);
        broadcast(new UserTyping($user, $conversationId, $isTyping));
    }

    /**
     * Update user online status
     */
    public function updateOnlineStatus(int $userId, bool $isOnline, ?string $statusMessage = null, array $metadata = []): void
    {
        UserOnlineStatus::updateStatus($userId, $isOnline, $statusMessage, $metadata);
        
        // Update user's last_seen_at
        User::where('id', $userId)->update(['last_seen_at' => now()]);
        
        $user = User::find($userId);
        broadcast(new UserOnlineStatusChanged($user, $isOnline, now()));
    }

    /**
     * Get online users excluding current user
     */
    public function getOnlineUsers(int $excludeUserId): Collection
    {
        return UserOnlineStatus::online()
            ->with('user:id,name,avatar')
            ->whereHas('user', function ($query) use ($excludeUserId) {
                $query->where('id', '!=', $excludeUserId);
            })
            ->get()
            ->map(function ($status) {
                return [
                    'id' => $status->user->id,
                    'name' => $status->user->name,
                    'avatar' => $status->user->avatar 
                        ? asset('storage/' . $status->user->avatar) 
                        : asset('images/default-avatar.png'),
                    'status_message' => $status->status_message,
                    'last_seen_at' => $status->last_seen_at->toISOString(),
                ];
            });
    }

    /**
     * Search users for new conversations
     */
    public function searchUsers(string $query, int $excludeUserId): Collection
    {
        if (strlen($query) < 2) {
            return collect();
        }

        return User::where('id', '!=', $excludeUserId)
            ->where(function ($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('email', 'LIKE', "%{$query}%")
                  ->orWhere('username', 'LIKE', "%{$query}%");
            })
            ->select('id', 'name', 'email', 'username', 'avatar')
            ->limit(20)
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'username' => $user->username,
                    'avatar' => $user->avatar 
                        ? asset('storage/' . $user->avatar) 
                        : asset('images/default-avatar.png'),
                    'is_online' => UserOnlineStatus::isOnline($user->id),
                ];
            });
    }

    /**
     * Add reaction to message
     */
    public function addMessageReaction(int $messageId, int $userId, string $emoji): array
    {
        $message = Message::findOrFail($messageId);
        
        if (!$message->conversation->hasParticipant($userId)) {
            throw new \Exception('Unauthorized access to message');
        }

        $message->addReaction($emoji, $userId);
        
        return $message->fresh()->reactions ?? [];
    }

    /**
     * Get typing indicators for conversation
     */
    public function getTypingIndicators(int $conversationId, int $excludeUserId): Collection
    {
        $conversation = Conversation::findOrFail($conversationId);
        
        if (!$conversation->hasParticipant($excludeUserId)) {
            throw new \Exception('Unauthorized access to conversation');
        }

        return TypingIndicator::getActiveTypingInConversation($conversationId, $excludeUserId)
            ->map(function ($indicator) {
                return [
                    'user_id' => $indicator->user_id,
                    'user_name' => $indicator->user->name,
                    'started_at' => $indicator->started_at->toISOString(),
                ];
            });
    }

    /**
     * Private helper methods
     */
    private function getOrCreateConversation(MessageData $messageData): Conversation
    {
        if ($messageData->conversationId) {
            return Conversation::findOrFail($messageData->conversationId);
        }
        
        if ($messageData->recipientId) {
            return Conversation::findOrCreateBetweenUsers($messageData->senderId, $messageData->recipientId);
        }
        
        throw new \Exception('Either conversation_id or recipient_id is required');
    }

    private function formatConversationForUser(Conversation $conversation, int $userId): array
    {
        $otherUser = $conversation->otherParticipant($userId);
        $isOnline = $otherUser ? UserOnlineStatus::isOnline($otherUser->id) : false;
        
        return [
            'id' => $conversation->id,
            'title' => $conversation->getDisplayNameForUser($userId),
            'avatar' => $conversation->getAvatarForUser($userId),
            'other_user_id' => $otherUser?->id,
            'is_online' => $isOnline,
            'last_message' => $conversation->lastMessage ? [
                'id' => $conversation->lastMessage->id,
                'content' => $conversation->lastMessage->content,
                'message_type' => $conversation->lastMessage->message_type,
                'sender_name' => $conversation->lastMessage->sender->name,
                'sender_id' => $conversation->lastMessage->sender_id,
                'created_at' => $conversation->lastMessage->created_at->toISOString(),
                'is_mine' => $conversation->lastMessage->sender_id === $userId,
            ] : null,
            'unread_count' => $conversation->getUnreadCountForUser($userId),
            'last_activity' => $conversation->last_message_at 
                ? $conversation->last_message_at->toISOString() 
                : $conversation->updated_at->toISOString(),
        ];
    }

    private function formatMessage(Message $message, int $userId): array
    {
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
            'sender_avatar' => $message->sender->avatar 
                ? asset('storage/' . $message->sender->avatar) 
                : asset('images/default-avatar.png'),
            'is_mine' => $message->sender_id === $userId,
            'is_read' => $message->is_read,
            'read_by' => $message->read_by,
            'is_read_by_me' => $message->isReadBy($userId),
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
    }

    private function markConversationAsReadForUser(Conversation $conversation, int $userId): void
    {
        $conversation->markAsReadForUser($userId);
    }

    private function determineMessageTypeFromFile(UploadedFile $file): string
    {
        $mimeType = $file->getMimeType();
        
        if (str_starts_with($mimeType, 'image/')) {
            return 'image';
        } elseif (str_starts_with($mimeType, 'video/')) {
            return 'video';
        } elseif (str_starts_with($mimeType, 'audio/')) {
            return 'audio';
        }
        
        return 'file';
    }
}
