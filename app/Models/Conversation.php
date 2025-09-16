<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Conversation extends Model
{
    protected $fillable = [
        'type',
        'title',
        'description',
        'created_by',
        'is_archived',
        'last_activity_at',
        'metadata',
        // legacy direct columns for compatibility with broadcasting and uniqueness
        'user1_id',
        'user2_id',
        // last message reference
        'last_message_id',
        'last_message_at',
    ];

    protected $casts = [
        'last_activity_at' => 'datetime',
        'is_archived' => 'boolean',
        'metadata' => 'array',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'last_activity_at',
    ];

    /**
     * Get the user who created the conversation
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * User1 (legacy direct conversation participant)
     */
    public function user1(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user1_id');
    }

    /**
     * User2 (legacy direct conversation participant)
     */
    public function user2(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user2_id');
    }

    /**
     * Get all messages in this conversation
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class)
            ->where(function ($q) {
                $q->whereNull('is_deleted')->orWhere('is_deleted', false);
            })
            ->orderBy('created_at', 'desc');
    }

    /**
     * Relationship to the conversation's last message (via last_message_id)
     */
    public function lastMessage(): BelongsTo
    {
        return $this->belongsTo(Message::class, 'last_message_id');
    }

    /**
     * Get the latest message in this conversation (query helper)
     */
    public function latestMessage(): HasMany
    {
        return $this->hasMany(Message::class)
            ->where(function ($q) {
                $q->whereNull('is_deleted')->orWhere('is_deleted', false);
            })
            ->latest()
            ->limit(1);
    }

    /**
     * Get all participants in the conversation
     */
    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'conversation_participants')
            ->withPivot('joined_at', 'last_read_at', 'role', 'is_muted', 'can_add_participants')
            ->withTimestamps();
    }

    /**
     * Get typing indicators for this conversation
     */
    public function typingIndicators(): HasMany
    {
        return $this->hasMany(TypingIndicator::class)
            ->where('expires_at', '>', now());
    }

    /**
     * Get users currently typing
     */
    public function usersTyping()
    {
        return $this->typingIndicators()
            ->with('user')
            ->get()
            ->pluck('user');
    }

    /**
     * Check if a user is participant in this conversation
     */
    public function hasParticipant(int $userId): bool
    {
        // Check pivot participants (new system)
        if ($this->participants()->where('user_id', $userId)->exists()) {
            return true;
        }
        // Fallback to legacy direct columns (user1_id/user2_id)
        if (!is_null($this->user1_id) && !is_null($this->user2_id)) {
            return ($this->user1_id === $userId) || ($this->user2_id === $userId);
        }
        return false;
    }

    /**
     * Add a participant to the conversation
     */
    public function addParticipant(int $userId, string $role = 'participant'): void
    {
        if (!$this->hasParticipant($userId)) {
            $this->participants()->attach($userId, [
                'joined_at' => now(),
                'role' => $role,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Remove a participant from the conversation
     */
    public function removeParticipant(int $userId): void
    {
        $this->participants()->detach($userId);
    }

    /**
     * Get unread messages count for a specific user
     */
    public function getUnreadCountForUser(int $userId): int
    {
        $participant = $this->participants()->where('user_id', $userId)->first();
        if (!$participant) {
            return 0;
        }

        $lastReadAt = $participant->pivot->last_read_at;
        
        return $this->messages()
            ->where(function ($q) use ($userId) {
                // Support both sender_id and user_id message columns
                $q->where('sender_id', '!=', $userId)
                  ->orWhere(function ($qq) use ($userId) {
                      $qq->whereNull('sender_id')->where('user_id', '!=', $userId);
                  });
            })
            ->when($lastReadAt, function ($query) use ($lastReadAt) {
                return $query->where('created_at', '>', $lastReadAt);
            })
            ->count();
    }

    /**
     * Mark conversation as read for a user
     */
    public function markAsReadForUser(int $userId): void
    {
        $this->participants()->updateExistingPivot($userId, [
            'last_read_at' => now(),
        ]);
    }

    /**
     * Update last activity timestamp
     */
    public function updateActivity(): void
    {
        $this->update(['last_activity_at' => now()]);
    }

    /**
     * Update last message references and timestamps
     */
    public function updateLastMessage(Message $message): void
    {
        $this->last_message_id = $message->id;
        $this->last_message_at = $message->created_at ?? now();
        $this->last_activity_at = now();
        $this->save();
    }

    /**
     * Get conversation display name for a user
     */
    public function getDisplayNameForUser(int $userId): string
    {
        if ($this->title) {
            return $this->title;
        }

        // For private/direct conversations, show the other participant's name
        $isPrivate = ($this->type ?? null) === 'private' || ($this->conversation_type ?? null) === 'direct';
        if ($isPrivate) {
            $other = $this->otherParticipant($userId);
            return $other ? $other->name : 'Private Conversation';
        }

        return 'Group Conversation';
    }

    /**
     * Get conversation avatar for a user
     */
    public function getAvatarForUser(int $userId): string
    {
        // For private/direct conversations, show the other participant's avatar
        $isPrivate = ($this->type ?? null) === 'private' || ($this->conversation_type ?? null) === 'direct';
        if ($isPrivate) {
            $other = $this->otherParticipant($userId);
            return $other && method_exists($other, 'getProfilePictureUrl') 
                ? $other->getProfilePictureUrl() 
                : asset('images/default-avatar.png');
        }

        return asset('images/group-avatar.png');
    }

    /**
     * Find the other participant in a direct/private conversation
     */
    public function otherParticipant(int $userId)
    {
        // Prefer participants pivot (new system)
        $other = $this->participants()
            ->where('user_id', '!=', $userId)
            ->first();
        if ($other) {
            return $other;
        }
        // Fallback to user1/user2 columns (legacy)
        $otherId = null;
        if (!is_null($this->user1_id) && !is_null($this->user2_id)) {
            $otherId = $this->user1_id === $userId ? $this->user2_id : $this->user1_id;
        }
        return $otherId ? User::find($otherId) : null;
    }

    /**
     * Scope for conversations involving a specific user
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where(function ($q) use ($userId) {
            $q->whereHas('participants', function ($qq) use ($userId) {
                $qq->where('user_id', $userId);
            })
            ->orWhere(function ($qq) use ($userId) {
                $qq->where('user1_id', $userId)->orWhere('user2_id', $userId);
            });
        });
    }

    /**
     * Scope for active conversations
     */
    public function scopeActive($query)
    {
        return $query->where('is_archived', false);
    }

    /**
     * Create or find private conversation between two users
     */
    public static function findOrCreateBetweenUsers(int $user1Id, int $user2Id): self
    {
        // First try to find existing private conversation between these users (pivot-based)
        $conversation = self::where(function ($q) {
                $q->where('type', 'private')
                  ->orWhere('conversation_type', 'direct');
            })
            ->whereHas('participants', function ($query) use ($user1Id) {
                $query->where('user_id', $user1Id);
            })
            ->whereHas('participants', function ($query) use ($user2Id) {
                $query->where('user_id', $user2Id);
            })
            ->first();

        if ($conversation) {
            return $conversation;
        }

        // Normalize ordering to satisfy unique index on (user1_id, user2_id)
        [$a, $b] = $user1Id < $user2Id ? [$user1Id, $user2Id] : [$user2Id, $user1Id];

        // Create new conversation
        $conversation = self::create([
            'type' => 'private',
            'created_by' => $user1Id,
            'last_activity_at' => now(),
            'user1_id' => $a,
            'user2_id' => $b,
        ]);

        // Add both users as participants
        $conversation->addParticipant($user1Id);
        $conversation->addParticipant($user2Id);

        return $conversation;
    }
}

