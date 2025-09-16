<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use SoftDeletes;

    // Support both legacy and new fields to maintain compatibility with v1 API and realtime UI
    protected $fillable = [
        'conversation_id',
        // sender/user columns
        'sender_id',
        'user_id',
        // content/type columns
        'content',
        'type',
        'message_type',
        // reply/references
        'reply_to_id',
        // file columns (both variants)
        'file_url',
        'file_name',
        'file_size',
        'file_type',
        'file_path',
        // state/status
        'status',
        'local_id',
        'is_read',
        'is_edited',
        'is_deleted',
        'edited_at',
        'deleted_at',
        'delivered_at',
        'read_at',
        'call_duration',
        'is_system',
        'thread_id',
        // reactions/reads/attachments/metadata
        'reactions',
        'read_by',
        'attachments',
        'metadata',
    ];

    protected $casts = [
        // flags
        'is_edited' => 'boolean',
        'is_deleted' => 'boolean',
        'is_read' => 'boolean',
        'is_system' => 'boolean',
        // arrays
        'attachments' => 'array',
        'metadata' => 'array',
        'read_by' => 'array',
        'reactions' => 'array',
        // dates
        'edited_at' => 'datetime',
        'deleted_at' => 'datetime',
        'delivered_at' => 'datetime',
        'read_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'edited_at',
        'delivered_at',
        'read_at',
    ];

    /**
     * Get the conversation this message belongs to
     */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    /**
     * Get the user who sent this message (legacy relation)
     */
    public function user(): BelongsTo
    {
        // Prefer sender_id if present, otherwise fall back to user_id
        $foreignKey = $this->getAttribute('sender_id') !== null ? 'sender_id' : 'user_id';
        return $this->belongsTo(User::class, $foreignKey);
    }

    /**
     * Sender relation (explicit for new UI)
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
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
        if (!is_array($readBy)) {
            $decoded = json_decode((string) $this->read_by, true);
            $readBy = is_array($decoded) ? $decoded : [];
        }
        if (!in_array($userId, $readBy)) {
            $readBy[] = $userId;
            $this->update(['read_by' => $readBy]);
        }
        
        // If it's a direct conversation and both participants have read it
        $conversation = $this->conversation;
        if ($conversation) {
            $isDirect = ($conversation->conversation_type ?? null) === 'direct' || ($conversation->type ?? null) === 'private';
            if ($isDirect) {
                $participants = array_values(array_filter([
                    $conversation->user1_id ?? null,
                    $conversation->user2_id ?? null,
                ], fn ($v) => !is_null($v)));
                if (count($participants) >= 2 && count(array_intersect($readBy, $participants)) === 2) {
                    $this->update(['is_read' => true]);
                }
            }
        }
    }

    /**
     * Check if message has been read by a specific user
     */
    public function isReadBy($userId): bool
    {
        $readBy = $this->read_by ?? [];
        if (!is_array($readBy)) {
            $decoded = json_decode((string) $this->read_by, true);
            $readBy = is_array($decoded) ? $decoded : [];
        }
        return in_array($userId, $readBy);
    }

    /**
     * Add reaction to message (JSON aggregation variant)
     */
    public function addReaction($emoji, $userId)
    {
        $reactions = $this->reactions ?? [];
        if (!is_array($reactions)) {
            $decoded = json_decode((string) $this->reactions, true);
            $reactions = is_array($decoded) ? $decoded : [];
        }
        
        // Find existing reaction with this emoji
        $reactionIndex = null;
        foreach ($reactions as $index => $reaction) {
            if (($reaction['emoji'] ?? null) === $emoji) {
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

        $bytes = (int) $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes > 1024 && $i < count($units) - 1) {
            $bytes = $bytes / 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Check if message is a file/media message
     */
    public function isFileMessage(): bool
    {
        $type = $this->message_type ?? $this->type;
        return in_array($type, ['file', 'image', 'video', 'audio']);
    }

    /**
     * Get message preview text for conversations list
     */
    public function getPreviewTextAttribute(): string
    {
        $type = $this->message_type ?? $this->type;
        switch ($type) {
            case 'image':
                return '📷 Image';
            case 'video':
                return '🎥 Video';
            case 'audio':
                return '🎵 Audio';
            case 'file':
                return '📎 File';
            case 'call':
                return '📞 ' . ($this->content ?? 'Call');
            default:
                return $this->content ?? '';
        }
    }
    
    /**
     * Reactions relation (table-backed variant)
     */
    public function reactions(): HasMany
    {
        return $this->hasMany(MessageReaction::class);
    }
    
    /**
     * Mentions relation
     */
    public function mentions(): HasMany
    {
        return $this->hasMany(MessageMention::class);
    }
    
    /**
     * Grouped reactions accessor (table-backed)
     */
    public function getGroupedReactionsAttribute()
    {
        return MessageReaction::getGroupedReactions($this->id);
    }
    
    /**
     * Toggle reaction on this message (table-backed)
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
        }
        $this->reactions()->create([
            'user_id' => $userId,
            'emoji' => $emoji
        ]);
        return 'added';
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
        return (bool) ($this->conversation->encryption_key ?? false);
    }
    
    /**
     * Get decrypted content if encrypted (placeholder)
     */
    public function getDecryptedContent($userKey = null): string
    {
        if (!$this->isEncrypted()) {
            return (string) ($this->content ?? '');
        }
        // TODO: Implement encryption/decryption logic
        return (string) ($this->content ?? '');
    }
}
