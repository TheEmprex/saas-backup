<?php

namespace App\Events;

use App\Models\User;
use App\Models\UserOnlineStatus;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserOnlineStatusChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public User $user;
    public bool $isOnline;
    public \Carbon\Carbon $lastSeenAt;

    /**
     * Create a new event instance.
     */
    public function __construct(User $user, bool $isOnline, ?\Carbon\Carbon $lastSeenAt = null)
    {
        $this->user = $user;
        $this->isOnline = $isOnline;
        $this->lastSeenAt = $lastSeenAt ?: now();
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new PresenceChannel('online-users'),
            new Channel('user-status.' . $this->user->id),
        ];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'user_id' => $this->user->id,
            'user_name' => $this->user->name,
            'user_avatar' => $this->user->avatar ? asset('storage/' . $this->user->avatar) : asset('images/default-avatar.png'),
            'is_online' => $this->isOnline,
            'last_seen_at' => $this->lastSeenAt->toISOString(),
            'formatted_last_seen' => $this->isOnline ? 'Online' : $this->lastSeenAt->diffForHumans(),
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'user.status.changed';
    }
}
