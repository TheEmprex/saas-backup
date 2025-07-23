<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReviewContest extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'rating_id',
        'contested_by',
        'reason',
        'evidence',
        'status',
        'reviewed_by',
        'admin_notes',
        'resolved_at'
    ];
    
    protected $casts = [
        'resolved_at' => 'datetime'
    ];
    
    public function rating(): BelongsTo
    {
        return $this->belongsTo(Rating::class);
    }
    
    public function contestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'contested_by');
    }
    
    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
    
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }
    
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }
    
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }
}
