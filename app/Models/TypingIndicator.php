<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class TypingIndicator extends Model
{
    protected $table = 'typing_indicators';

    protected $fillable = [
        'conversation_id',
        'user_id',
        'expires_at'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'started_at',
        'expires_at',
    ];

    /**
     * Get the conversation this typing indicator belongs to
     */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    /**
     * Get the user who is typing
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Start typing indicator for a user in a conversation
     */
    public static function startTyping(int $conversationId, int $userId): void
    {
        self::updateOrCreate(
            [
                'conversation_id' => $conversationId,
                'user_id' => $userId,
            ],
            [
                'expires_at' => now()->addSeconds(10), // Auto-expire after 10 seconds
            ]
        );
    }

    /**
     * Stop typing indicator for a user in a conversation
     */
    public static function stopTyping(int $conversationId, int $userId): void
    {
        self::where('conversation_id', $conversationId)
            ->where('user_id', $userId)
            ->delete();
    }

    /**
     * Get active typing indicators for a conversation (excluding a specific user)
     */
    public static function getActiveTypingInConversation(int $conversationId, ?int $excludeUserId = null): \Illuminate\Database\Eloquent\Collection
    {
        $query = self::with('user')
            ->where('conversation_id', $conversationId)
            ->where('expires_at', '>', now());

        if ($excludeUserId) {
            $query->where('user_id', '!=', $excludeUserId);
        }

        return $query->get();
    }

    /**
     * Check if a user is currently typing in a conversation
     */
    public static function isUserTyping(int $conversationId, int $userId): bool
    {
        return self::where('conversation_id', $conversationId)
            ->where('user_id', $userId)
            ->where('expires_at', '>', now())
            ->exists();
    }

    /**
     * Clean up expired typing indicators
     */
    public static function cleanupExpired(): int
    {
        return self::where('expires_at', '<=', now())->delete();
    }

    /**
     * Scope for active typing indicators
     */
    public function scopeActive($query)
    {
        return $query->where('expires_at', '>', now());
    }

    /**
     * Scope for expired typing indicators
     */
    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<=', now());
    }
}
