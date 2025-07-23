<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Earning extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'contract_id',
        'amount',
        'currency',
        'type',
        'status',
        'description',
        'earned_date',
        'paid_date',
        'metadata'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'earned_date' => 'date',
        'paid_date' => 'date',
        'metadata' => 'array'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('earned_date', now()->month)
                    ->whereYear('earned_date', now()->year);
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('earned_date', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('earned_date', now()->toDateString());
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
