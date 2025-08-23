<?php

namespace App\DTOs;

use Carbon\Carbon;

class ConversationData
{
    public function __construct(
        public readonly int $id,
        public readonly string $title,
        public readonly string $avatar,
        public readonly ?int $otherUserId = null,
        public readonly bool $isOnline = false,
        public readonly ?array $lastMessage = null,
        public readonly int $unreadCount = 0,
        public readonly ?Carbon $lastActivity = null,
        public readonly string $conversationType = 'direct',
        public readonly bool $isArchived = false,
        public readonly ?array $metadata = null
    ) {}

    public static function fromModel(\App\Models\Conversation $conversation, int $userId): self
    {
        $otherUser = $conversation->otherParticipant($userId);
        
        return new self(
            id: $conversation->id,
            title: $conversation->getDisplayNameForUser($userId),
            avatar: $conversation->getAvatarForUser($userId),
            otherUserId: $otherUser?->id,
            isOnline: $otherUser ? \App\Models\UserOnlineStatus::isOnline($otherUser->id) : false,
            lastMessage: $conversation->lastMessage ? [
                'id' => $conversation->lastMessage->id,
                'content' => $conversation->lastMessage->content,
                'message_type' => $conversation->lastMessage->message_type,
                'sender_name' => $conversation->lastMessage->sender->name,
                'sender_id' => $conversation->lastMessage->sender_id,
                'created_at' => $conversation->lastMessage->created_at->toISOString(),
                'is_mine' => $conversation->lastMessage->sender_id === $userId,
            ] : null,
            unreadCount: $conversation->getUnreadCountForUser($userId),
            lastActivity: $conversation->last_message_at ?: $conversation->updated_at,
            conversationType: $conversation->conversation_type ?? 'direct',
            isArchived: $conversation->is_archived ?? false,
            metadata: $conversation->metadata
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'avatar' => $this->avatar,
            'other_user_id' => $this->otherUserId,
            'is_online' => $this->isOnline,
            'last_message' => $this->lastMessage,
            'unread_count' => $this->unreadCount,
            'last_activity' => $this->lastActivity?->toISOString(),
            'conversation_type' => $this->conversationType,
            'is_archived' => $this->isArchived,
            'metadata' => $this->metadata,
        ];
    }

    public function hasUnreadMessages(): bool
    {
        return $this->unreadCount > 0;
    }

    public function isUserOnline(): bool
    {
        return $this->isOnline;
    }
}
