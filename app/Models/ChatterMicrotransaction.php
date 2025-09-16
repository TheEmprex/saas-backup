<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatterMicrotransaction extends Model
{
    protected $fillable = [
        'user_id',
        'job_post_id',
        'amount',
        'type',
        'status',
        'payment_method',
        'transaction_id',
        'expires_at',
    ];

    protected $casts = [
        'amount' => 'float',
        'expires_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function jobPost()
    {
        return $this->belongsTo(JobPost::class);
    }

    public function isActive(): bool
    {
        return $this->expires_at === null || $this->expires_at->isFuture();
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }
}
