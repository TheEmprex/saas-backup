<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'conversation_id',
        'user_id',
        'content',
        'type',
        'reply_to_id',
        'is_edited',
        'edited_at',
        'is_deleted',
        'deleted_at',
        'attachments',
        'metadata'
    ];

    protected $casts = [
        'is_edited' => 'boolean',
        'is_deleted' => 'boolean',
        'attachments' => 'array',
        'metadata' => 'array',
        'edited_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'edited_at',
    ];

    /**
     * Get the conversation this message belongs to
     */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    /**
     * Get the user who sent this message
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the user who sent this message (alias)
     */
    public function sender(): BelongsTo
    {
        return $this->user();
    }

    /**
     * Get the message this is replying to
     */
    public function replyTo(): BelongsTo
    {
        return $this->belongsTo(Message::class, 'reply_to_id');
    }

    /**
     * Get replies to this message
     */
    public function replies(): HasMany
    {
        return $this->hasMany(Message::class, 'reply_to_id');
    }

    /**
     * Scope to get messages for a specific conversation
     */
    public function scopeForConversation($query, $conversationId)
    {
        return $query->where('conversation_id', $conversationId);
    }

    /**
     * Scope to get unread messages
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Mark message as read by a user
     */
    public function markAsReadBy($userId)
    {
        $readBy = $this->read_by ?? [];
        if (!in_array($userId, $readBy)) {
            $readBy[] = $userId;
            $this->update(['read_by' => $readBy]);
        }
        
        // If it's a direct conversation and both participants have read it
        $conversation = $this->conversation;
        if ($conversation->conversation_type === 'direct') {
            $participants = [$conversation->user1_id, $conversation->user2_id];
            if (count(array_intersect($readBy, $participants)) === 2) {
                $this->update(['is_read' => true]);
            }
        }
    }

    /**
     * Check if message has been read by a specific user
     */
    public function isReadBy($userId): bool
    {
        return in_array($userId, $this->read_by ?? []);
    }

    /**
     * Add reaction to message
     */
    public function addReaction($emoji, $userId)
    {
        $reactions = $this->reactions ?? [];
        
        // Find existing reaction with this emoji
        $reactionIndex = null;
        foreach ($reactions as $index => $reaction) {
            if ($reaction['emoji'] === $emoji) {
                $reactionIndex = $index;
                break;
            }
        }

        if ($reactionIndex !== null) {
            // Reaction exists, toggle user
            $userIds = $reactions[$reactionIndex]['user_ids'] ?? [];
            $userIndex = array_search($userId, $userIds);
            
            if ($userIndex !== false) {
                // Remove user's reaction
                array_splice($userIds, $userIndex, 1);
                if (empty($userIds)) {
                    // Remove entire reaction if no users left
                    array_splice($reactions, $reactionIndex, 1);
                } else {
                    $reactions[$reactionIndex]['user_ids'] = $userIds;
                }
            } else {
                // Add user's reaction
                $reactions[$reactionIndex]['user_ids'][] = $userId;
            }
        } else {
            // New reaction
            $reactions[] = [
                'emoji' => $emoji,
                'user_ids' => [$userId]
            ];
        }

        $this->update(['reactions' => $reactions]);
    }

    /**
     * Get formatted file size
     */
    public function getFormattedFileSizeAttribute(): ?string
    {
        if (!$this->file_size) {
            return null;
        }

        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Check if message is a file/media message
     */
    public function isFileMessage(): bool
    {
        return in_array($this->message_type, ['file', 'image', 'video', 'audio']);
    }

    /**
     * Get message preview text for conversations list
     */
    public function getPreviewTextAttribute(): string
    {
        switch ($this->message_type) {
            case 'image':
                return '📷 Image';
            case 'video':
                return '🎥 Video';
            case 'audio':
                return '🎵 Audio';
            case 'file':
                return '📎 File';
            case 'call':
                return '📞 ' . $this->content;
            default:
                return $this->content ?? '';
        }
    }
    
    /**
     * Get reactions for this message
     */
    public function reactions(): HasMany
    {
        return $this->hasMany(MessageReaction::class);
    }
    
    /**
     * Get mentions for this message
     */
    public function mentions(): HasMany
    {
        return $this->hasMany(MessageMention::class);
    }
    
    /**
     * Get grouped reactions for this message
     */
    public function getGroupedReactionsAttribute()
    {
        return MessageReaction::getGroupedReactions($this->id);
    }
    
    /**
     * Toggle reaction on this message
     */
    public function toggleReaction($emoji, $userId)
    {
        $existing = $this->reactions()
            ->where('user_id', $userId)
            ->where('emoji', $emoji)
            ->first();
            
        if ($existing) {
            $existing->delete();
            return 'removed';
        } else {
            $this->reactions()->create([
                'user_id' => $userId,
                'emoji' => $emoji
            ]);
            return 'added';
        }
    }
    
    /**
     * Update message status
     */
    public function updateStatus($status, $userId = null)
    {
        $this->update(['status' => $status]);
        
        if ($status === 'delivered' && !$this->delivered_at) {
            $this->update(['delivered_at' => now()]);
        }
        
        if ($status === 'read' && !$this->read_at) {
            $this->update(['read_at' => now()]);
        }
    }
    
    /**
     * Check if message is encrypted
     */
    public function isEncrypted(): bool
    {
        return $this->conversation->encryption_key !== null;
    }
    
    /**
     * Get decrypted content if encrypted
     */
    public function getDecryptedContent($userKey = null): string
    {
        if (!$this->isEncrypted()) {
            return $this->content;
        }
        
        // TODO: Implement encryption/decryption logic
        return $this->content;
    }
}
