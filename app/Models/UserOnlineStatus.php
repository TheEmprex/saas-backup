<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class UserOnlineStatus extends Model
{
    protected $table = 'user_online_status';

    protected $fillable = [
        'user_id',
        'is_online',
        'last_seen_at',
        'status_message',
        'device_info'
    ];

    protected $casts = [
        'is_online' => 'boolean',
        'last_seen_at' => 'datetime',
        'device_info' => 'array',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'last_seen_at',
    ];

    /**
     * Get the user this status belongs to
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Update user online status
     */
    public static function updateStatus(int $userId, bool $isOnline, ?string $statusMessage = null, ?array $deviceInfo = null): void
    {
        self::updateOrCreate(
            ['user_id' => $userId],
            [
                'is_online' => $isOnline,
                'last_seen_at' => now(),
                'status_message' => $statusMessage,
                'device_info' => $deviceInfo,
            ]
        );
    }

    /**
     * Mark user as offline
     */
    public static function markOffline(int $userId): void
    {
        self::updateOrCreate(
            ['user_id' => $userId],
            [
                'is_online' => false,
                'last_seen_at' => now(),
            ]
        );
    }

    /**
     * Get online status for user
     */
    public static function getStatus(int $userId): ?self
    {
        return self::where('user_id', $userId)->first();
    }

    /**
     * Check if user is currently online
     */
    public static function isOnline(int $userId): bool
    {
        $status = self::getStatus($userId);
        
        if (!$status || !$status->is_online) {
            return false;
        }

        // Consider user offline if last seen more than 5 minutes ago
        return $status->last_seen_at && $status->last_seen_at->diffInMinutes(now()) <= 5;
    }

    /**
     * Get formatted last seen time
     */
    public function getFormattedLastSeenAttribute(): string
    {
        if (!$this->last_seen_at) {
            return 'Never';
        }

        if ($this->is_online && $this->last_seen_at->diffInMinutes(now()) <= 5) {
            return 'Online';
        }

        return $this->last_seen_at->diffForHumans();
    }

    /**
     * Scope for online users
     */
    public function scopeOnline($query)
    {
        return $query->where('is_online', true)
            ->where('last_seen_at', '>=', now()->subMinutes(5));
    }

    /**
     * Scope for offline users
     */
    public function scopeOffline($query)
    {
        return $query->where(function ($q) {
            $q->where('is_online', false)
              ->orWhere('last_seen_at', '<', now()->subMinutes(5))
              ->orWhereNull('last_seen_at');
        });
    }
}
