<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EarningsVerification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'platform_name',
        'platform_username',
        'monthly_earnings',
        'earnings_screenshot_path',
        'profile_screenshot_path',
        'additional_notes',
        'status',
        'rejection_reason',
        'verified_at',
    ];

    protected $casts = [
        'monthly_earnings' => 'decimal:2',
        'verified_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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
