<?php

namespace App\Models\QueryBuilders;

use Illuminate\Database\Eloquent\Builder;

class ConversationQueryBuilder extends Builder
{
    /**
     * Scope conversations for a specific user with optimized eager loading
     */
    public function forUserOptimized(int $userId): static
    {
        return $this->where(function ($query) use ($userId) {
            $query->where('user1_id', $userId)
                  ->orWhere('user2_id', $userId);
        })
        ->with([
            'user1:id,name,avatar',
            'user2:id,name,avatar',
            'lastMessage' => function ($query) {
                $query->select('id', 'conversation_id', 'content', 'sender_id', 'created_at', 'message_type')
                      ->with('sender:id,name');
            }
        ])
        ->orderBy('last_message_at', 'desc')
        ->orderBy('updated_at', 'desc');
    }

    /**
     * Scope for active conversations (not archived)
     */
    public function active(): static
    {
        return $this->where('is_archived', false);
    }

    /**
     * Scope for conversations with unread messages for a user
     */
    public function withUnreadMessages(int $userId): static
    {
        return $this->whereHas('messages', function ($query) use ($userId) {
            $query->where('sender_id', '!=', $userId)
                  ->whereJsonDoesntContain('read_by', $userId);
        });
    }

    /**
     * Scope for conversations updated since a specific time
     */
    public function updatedSince(\Carbon\Carbon $since): static
    {
        return $this->where('updated_at', '>', $since);
    }

    /**
     * Get conversations with message count
     */
    public function withMessageCount(): static
    {
        return $this->withCount('messages');
    }

    /**
     * Get conversations with unread count for specific user
     */
    public function withUnreadCountForUser(int $userId): static
    {
        return $this->withCount([
            'messages as unread_count' => function ($query) use ($userId) {
                $query->where('sender_id', '!=', $userId)
                      ->whereJsonDoesntContain('read_by', $userId);
            }
        ]);
    }
}
