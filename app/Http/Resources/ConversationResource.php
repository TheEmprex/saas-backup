<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\UserOnlineStatus;

class ConversationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        $userId = $request->user()->id;
        $otherUser = $this->otherParticipant($userId);
        
        return [
            'id' => $this->id,
            'title' => $this->getDisplayNameForUser($userId),
            'avatar' => $this->getAvatarForUser($userId),
            'other_user_id' => $otherUser?->id,
            'other_user' => $otherUser ? [
                'id' => $otherUser->id,
                'name' => $otherUser->name,
                'avatar' => $otherUser->avatar 
                    ? asset('storage/' . $otherUser->avatar) 
                    : asset('images/default-avatar.png'),
                'is_online' => UserOnlineStatus::isOnline($otherUser->id),
            ] : null,
            'conversation_type' => $this->conversation_type ?? 'direct',
            'last_message' => $this->whenLoaded('lastMessage', function () use ($userId) {
                return [
                    'id' => $this->lastMessage->id,
                    'content' => $this->lastMessage->content,
                    'message_type' => $this->lastMessage->message_type,
                    'sender_name' => $this->lastMessage->sender->name,
                    'sender_id' => $this->lastMessage->sender_id,
                    'created_at' => $this->lastMessage->created_at->toISOString(),
                    'is_mine' => $this->lastMessage->sender_id === $userId,
                ];
            }),
            'unread_count' => $this->getUnreadCountForUser($userId),
            'last_activity' => $this->last_message_at 
                ? $this->last_message_at->toISOString() 
                : $this->updated_at->toISOString(),
            'is_archived' => $this->is_archived ?? false,
            'metadata' => $this->metadata,
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}
