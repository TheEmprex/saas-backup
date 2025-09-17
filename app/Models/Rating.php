<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rating extends Model
{
    use HasFactory;

    protected $fillable = [
        'rater_id',
        'rated_id',
        'job_post_id',
        'overall_rating',
        'communication_rating',
        'professionalism_rating',
        'timeliness_rating',
        'quality_rating',
        'review_title',
        'review_content',
        'conversion_rate_rating',
        'response_time_rating',
        'payment_reliability_rating',
        'expectation_clarity_rating',
        'is_verified',
        'is_public',
        'metrics',
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'is_verified' => 'boolean',
        'overall_rating' => 'integer',
        'communication_rating' => 'integer',
        'professionalism_rating' => 'integer',
        'timeliness_rating' => 'integer',
        'quality_rating' => 'integer',
        'conversion_rate_rating' => 'integer',
        'response_time_rating' => 'integer',
        'payment_reliability_rating' => 'integer',
        'expectation_clarity_rating' => 'integer',
        'metrics' => 'array',
    ];

    public static function calculateAverageRating(int $userId, ?string $ratingType = null): float
    {
        $query = static::query()->where('rated_id', $userId)
            ->where('is_public', true);

        // For now, just use overall_rating as we don't have rating_type column
        return round($query->avg('overall_rating') ?? 0, 1);
    }

    public static function getRatingBreakdown(int $userId): array
    {
        $ratings = static::query()->where('rated_id', $userId)
            ->where('is_public', true)
            ->selectRaw('overall_rating as rating, COUNT(*) as count')
            ->groupBy('overall_rating')
            ->pluck('count', 'rating')
            ->toArray();

        $breakdown = [];

        for ($i = 5; $i >= 1; $i--) {
            $breakdown[$i] = $ratings[$i] ?? 0;
        }

        return $breakdown;
    }

    public function rater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rater_id');
    }

    public function rated(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rated_id');
    }

    public function jobPost(): BelongsTo
    {
        return $this->belongsTo(JobPost::class);
    }

    public function getRatingTypeLabel(): string
    {
        return match ($this->rating_type) {
            'job_completion' => 'Job Completion',
            'communication' => 'Communication',
            'professionalism' => 'Professionalism',
            'overall' => 'Overall Experience',
            default => 'Unknown'
        };
    }

    public function getStarsHtml(): string
    {
        $stars = '';

        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $this->overall_rating) {
                $stars .= '<svg class="w-4 h-4 text-yellow-400 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>';
            } else {
                $stars .= '<svg class="w-4 h-4 text-gray-300 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>';
            }
        }

        return $stars;
    }
}
