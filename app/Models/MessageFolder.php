<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MessageFolder extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'name',
        'color',
        'sort_order'
    ];
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'folder_id');
    }
    
    public function getUnreadCount(): int
    {
        return $this->messages()->where('read', false)->count();
    }
}
