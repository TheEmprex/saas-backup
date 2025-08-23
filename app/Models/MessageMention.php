<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MessageMention extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'message_id',
        'user_id',
        'is_read'
    ];
    
    protected $casts = [
        'is_read' => 'boolean'
    ];
    
    /**
     * Get the message this mention belongs to
     */
    public function message(): BelongsTo
    {
        return $this->belongsTo(Message::class);
    }
    
    /**
     * Get the mentioned user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Mark mention as read
     */
    public function markAsRead()
    {
        $this->update(['is_read' => true]);
    }
    
    /**
     * Scope for unread mentions
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }
}
