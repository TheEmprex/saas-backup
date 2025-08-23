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
        'metadata'
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
     * Get all messages in this conversation
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class)
            ->where('is_deleted', false)
            ->orderBy('created_at', 'desc');
    }

    /**
     * Get the latest message in this conversation
     */
    public function latestMessage(): HasMany
    {
        return $this->hasMany(Message::class)
            ->where('is_deleted', false)
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
        return $this->participants()->where('user_id', $userId)->exists();
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
            ->where('user_id', '!=', $userId)
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
     * Get conversation display name for a user
     */
    public function getDisplayNameForUser(int $userId): string
    {
        if ($this->title) {
            return $this->title;
        }

        // For private conversations, show the other participant's name
        if ($this->type === 'private') {
            $otherParticipant = $this->participants()
                ->where('user_id', '!=', $userId)
                ->first();
            return $otherParticipant ? $otherParticipant->name : 'Private Conversation';
        }

        return 'Group Conversation';
    }

    /**
     * Get conversation avatar for a user
     */
    public function getAvatarForUser(int $userId): string
    {
        // For private conversations, show the other participant's avatar
        if ($this->type === 'private') {
            $otherParticipant = $this->participants()
                ->where('user_id', '!=', $userId)
                ->first();
            return $otherParticipant && method_exists($otherParticipant, 'getProfilePictureUrl') 
                ? $otherParticipant->getProfilePictureUrl() 
                : asset('images/default-avatar.png');
        }

        return asset('images/group-avatar.png');
    }

    /**
     * Scope for conversations involving a specific user
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->whereHas('participants', function ($q) use ($userId) {
            $q->where('user_id', $userId);
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
        // First try to find existing private conversation between these users
        $conversation = self::where('type', 'private')
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

        // Create new conversation
        $conversation = self::create([
            'type' => 'private',
            'created_by' => $user1Id,
            'last_activity_at' => now(),
        ]);

        // Add both users as participants
        $conversation->addParticipant($user1Id);
        $conversation->addParticipant($user2Id);

        return $conversation;
    }
}

