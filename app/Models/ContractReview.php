<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContractReview extends Model
{
    protected $fillable = [
        'contract_id',
        'reviewer_id',
        'reviewed_user_id',
        'rating',
        'comment',
        'skills_ratings',
        'would_work_again',
        'recommend_to_others',
    ];

    protected $casts = [
        'skills_ratings' => 'array',
        'would_work_again' => 'boolean',
        'recommend_to_others' => 'boolean',
    ];

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function reviewedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_user_id');
    }

    public function getStarsAttribute(): string
    {
        return str_repeat('★', $this->rating).str_repeat('☆', 5 - $this->rating);
    }
}
