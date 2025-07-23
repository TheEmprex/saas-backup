<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contract extends Model
{
    protected $fillable = [
        'employer_id',
        'contractor_id',
        'job_post_id',
        'contract_type',
        'rate',
        'commission_percentage',
        'description',
        'start_date',
        'end_date',
        'status',
        'approval_status',
        'approved_at',
        'rejected_at',
        'rejection_reason',
        'total_earned',
        'hours_worked',
        'earnings_log',
        'last_activity_at',
    ];

    protected $casts = [
        'earnings_log' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
        'last_activity_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'rate' => 'decimal:2',
        'commission_percentage' => 'decimal:2',
        'total_earned' => 'decimal:2',
    ];

    public function employer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employer_id');
    }

    public function contractor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'contractor_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(ContractReview::class);
    }

    public function addEarning(float $amount, string $description, ?int $hours = null): void
    {
        $earnings = $this->earnings_log ?? [];
        $earnings[] = [
            'amount' => $amount,
            'description' => $description,
            'hours' => $hours,
            'date' => now()->toDateString(),
            'created_at' => now()->toISOString(),
        ];
        
        $this->update([
            'earnings_log' => $earnings,
            'total_earned' => $this->total_earned + $amount,
            'hours_worked' => $this->hours_worked + ($hours ?? 0),
            'last_activity_at' => now(),
        ]);
    }

    public function getFormattedRateAttribute(): string
    {
        return match ($this->contract_type) {
            'hourly' => '$' . number_format($this->rate, 2) . '/hr',
            'fixed' => '$' . number_format($this->rate, 2),
            'commission' => number_format($this->commission_percentage, 1) . '%',
            default => 'N/A',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'active' => 'green',
            'completed' => 'blue',
            'cancelled' => 'red',
            'suspended' => 'yellow',
            default => 'gray',
        };
    }

    public function removeEarning(int $index): bool
    {
        $earnings = $this->earnings_log ?? [];
        
        if (!isset($earnings[$index])) {
            return false;
        }
        
        $removedEarning = $earnings[$index];
        unset($earnings[$index]);
        $earnings = array_values($earnings); // Reindex array
        
        $this->update([
            'earnings_log' => $earnings,
            'total_earned' => $this->total_earned - $removedEarning['amount'],
            'hours_worked' => $this->hours_worked - ($removedEarning['hours'] ?? 0),
            'last_activity_at' => now(),
        ]);
        
        return true;
    }

    public function canBeReviewedBy(User $user): bool
    {
        // Contract must be completed to be reviewed
        if ($this->status !== 'completed') {
            return false;
        }
        
        // User must be either employer or contractor
        if ($this->employer_id !== $user->id && $this->contractor_id !== $user->id) {
            return false;
        }
        
        // Check if user has already reviewed this contract
        return !$this->reviews()->where('reviewer_id', $user->id)->exists();
    }

    public function getOtherParty(User $user): ?User
    {
        if ($this->employer_id === $user->id) {
            return $this->contractor;
        } elseif ($this->contractor_id === $user->id) {
            return $this->employer;
        }
        
        return null;
    }

    public function canBeEditedBy(User $user): bool
    {
        return $this->employer_id === $user->id || $this->contractor_id === $user->id;
    }

    public function canBeDeletedBy(User $user): bool
    {
        // Only employer can delete contracts, and only if they haven't started
        return $this->employer_id === $user->id && $this->status === 'active' && $this->total_earned == 0;
    }

    public function jobPost(): BelongsTo
    {
        return $this->belongsTo(JobPost::class);
    }

    // Contract Approval Methods
    public function isPending(): bool
    {
        return $this->approval_status === 'pending';
    }

    public function isAccepted(): bool
    {
        return $this->approval_status === 'accepted';
    }

    public function isRejected(): bool
    {
        return $this->approval_status === 'rejected';
    }

    public function canBeApprovedBy(User $user): bool
    {
        // Only contractor can approve/reject contracts
        return $this->contractor_id === $user->id && $this->approval_status === 'pending';
    }

    public function accept(): bool
    {
        if ($this->approval_status !== 'pending') {
            return false;
        }

        return $this->update([
            'approval_status' => 'accepted',
            'approved_at' => now(),
            'status' => 'active', // Move from draft to active
            'rejected_at' => null,
            'rejection_reason' => null,
        ]);
    }

    public function reject(string $reason = null): bool
    {
        if ($this->approval_status !== 'pending') {
            return false;
        }

        return $this->update([
            'approval_status' => 'rejected',
            'rejected_at' => now(),
            'rejection_reason' => $reason,
            'status' => 'cancelled', // Move to cancelled status
            'approved_at' => null,
        ]);
    }

    public function getPendingContractsForUser(User $user)
    {
        return static::where('contractor_id', $user->id)
                    ->where('approval_status', 'pending')
                    ->with(['employer', 'jobPost'])
                    ->orderBy('created_at', 'desc')
                    ->get();
    }

    public function getApprovalStatusColorAttribute(): string
    {
        return match ($this->approval_status) {
            'pending' => 'yellow',
            'accepted' => 'green',
            'rejected' => 'red',
            default => 'gray',
        };
    }
}
