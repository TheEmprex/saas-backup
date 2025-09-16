<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SubscriptionPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subscription_type',
        'amount',
        'currency',
        'payment_method',
        'status',
        'transaction_id',
        'paid_at',
        'details',
    ];

    protected $casts = [
        'amount' => 'float',
        'paid_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function billable(): MorphTo
    {
        return $this->morphTo();
    }
}
