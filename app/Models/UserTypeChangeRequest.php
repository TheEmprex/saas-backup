<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserTypeChangeRequest extends Model
{
    protected $fillable = [
        'user_id',
        'current_user_type_id',
        'requested_user_type_id',
        'reason',
        'supporting_documents',
        'status',
        'admin_notes',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'supporting_documents' => 'array',
        'reviewed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function currentUserType(): BelongsTo
    {
        return $this->belongsTo(UserType::class, 'current_user_type_id');
    }

    public function requestedUserType(): BelongsTo
    {
        return $this->belongsTo(UserType::class, 'requested_user_type_id');
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

    public function approve(User $reviewedBy, ?string $notes = null): void
    {
        $this->update([
            'status' => 'approved',
            'admin_notes' => $notes,
            'reviewed_by' => $reviewedBy->id,
            'reviewed_at' => now(),
        ]);
        
        // Update user's type
        $this->user->update([
            'user_type_id' => $this->requested_user_type_id,
        ]);
    }

    public function reject(User $reviewedBy, ?string $notes = null): void
    {
        $this->update([
            'status' => 'rejected',
            'admin_notes' => $notes,
            'reviewed_by' => $reviewedBy->id,
            'reviewed_at' => now(),
        ]);
    }
}
