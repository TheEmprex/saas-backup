<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MessageReaction extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'message_id',
        'user_id', 
        'emoji'
    ];
    
    /**
     * Get the message this reaction belongs to
     */
    public function message(): BelongsTo
    {
        return $this->belongsTo(Message::class);
    }
    
    /**
     * Get the user who made this reaction
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Scope to get reactions for a specific message
     */
    public function scopeForMessage($query, $messageId)
    {
        return $query->where('message_id', $messageId);
    }
    
    /**
     * Get reactions grouped by emoji
     */
    public static function getGroupedReactions($messageId)
    {
        return self::where('message_id', $messageId)
            ->with('user:id,name,avatar')
            ->get()
            ->groupBy('emoji')
            ->map(function($reactions, $emoji) {
                return [
                    'emoji' => $emoji,
                    'count' => $reactions->count(),
                    'users' => $reactions->map(function($reaction) {
                        return [
                            'id' => $reaction->user->id,
                            'name' => $reaction->user->name,
                            'avatar' => $reaction->user->avatar ? asset('storage/' . $reaction->user->avatar) : null
                        ];
                    })
                ];
            })
            ->values();
    }
}
