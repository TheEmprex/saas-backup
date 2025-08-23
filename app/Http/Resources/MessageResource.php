<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'conversation_id' => $this->conversation_id,
            'content' => $this->content,
            'message_type' => $this->message_type,
            'file_url' => $this->file_url,
            'file_name' => $this->file_name,
            'file_size' => $this->file_size,
            'formatted_file_size' => $this->formatted_file_size,
            'sender_id' => $this->sender_id,
            'sender' => [
                'id' => $this->sender->id,
                'name' => $this->sender->name,
                'avatar' => $this->sender->avatar 
                    ? asset('storage/' . $this->sender->avatar) 
                    : asset('images/default-avatar.png'),
            ],
            'is_mine' => $this->sender_id === $request->user()?->id,
            'is_read' => $this->is_read,
            'read_by' => $this->read_by ?? [],
            'is_read_by_me' => $this->isReadBy($request->user()?->id ?? 0),
            'reply_to' => $this->whenLoaded('replyTo', function () {
                return [
                    'id' => $this->replyTo->id,
                    'content' => $this->replyTo->content,
                    'sender_name' => $this->replyTo->sender->name,
                ];
            }),
            'reactions' => $this->reactions ?? [],
            'metadata' => $this->metadata,
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
            'edited_at' => $this->edited_at?->toISOString(),
        ];
    }
}
