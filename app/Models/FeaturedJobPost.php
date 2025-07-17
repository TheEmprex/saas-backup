<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeaturedJobPost extends Model
{
    protected $fillable = [
        'user_id',
        'job_post_id',
        'amount_paid',
        'featured_until',
        'payment_status',
        'transaction_id',
    ];

    protected $casts = [
        'amount_paid' => 'float',
        'featured_until' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function jobPost()
    {
        return $this->belongsTo(JobPost::class);
    }

    public function isActive()
    {
        return $this->featured_until->isFuture();
    }

    public function isExpired()
    {
        return $this->featured_until->isPast();
    }
}
