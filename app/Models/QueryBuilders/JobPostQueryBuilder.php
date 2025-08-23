<?php

namespace App\Models\QueryBuilders;

use Illuminate\Database\Eloquent\Builder;

class JobPostQueryBuilder extends Builder
{
    /**
     * Scope for active jobs that haven't expired
     */
    public function activeAndNotExpired(): static
    {
        return $this->where('status', 'active')
                   ->where('expires_at', '>', now());
    }

    /**
     * Scope for featured jobs with priority ordering
     */
    public function featuredWithPriority(): static
    {
        return $this->orderBy('is_featured', 'desc')
                   ->orderBy('is_urgent', 'desc')
                   ->orderBy('created_at', 'desc');
    }

    /**
     * Scope with optimized relationships for listing
     */
    public function withOptimizedRelations(): static
    {
        return $this->with([
            'user:id,name,avatar',
            'user.userType:id,name',
            'applications' => function ($query) {
                $query->select('id', 'job_post_id', 'status')
                      ->limit(5);
            }
        ]);
    }

    /**
     * Scope with detailed relationships for single job view
     */
    public function withDetailedRelations(): static
    {
        return $this->with([
            'user:id,name,avatar',
            'user.userType:id,name',
            'user.userProfile:user_id,bio,rating,total_reviews',
            'applications' => function ($query) {
                $query->with('user:id,name,avatar')
                      ->orderBy('created_at', 'desc')
                      ->limit(10);
            },
            'ratings' => function ($query) {
                $query->with('rater:id,name')
                      ->orderBy('created_at', 'desc')
                      ->limit(5);
            }
        ]);
    }

    /**
     * Scope by market
     */
    public function byMarket(string $market): static
    {
        return $this->where('market', $market);
    }

    /**
     * Scope by experience level
     */
    public function byExperienceLevel(string $level): static
    {
        return $this->where('experience_level', $level);
    }

    /**
     * Scope by rate type
     */
    public function byRateType(string $rateType): static
    {
        return $this->where('rate_type', $rateType);
    }

    /**
     * Scope by rate range
     */
    public function byRateRange(?float $minRate, ?float $maxRate): static
    {
        if ($minRate !== null || $maxRate !== null) {
            $this->where(function ($query) use ($minRate, $maxRate) {
                if ($minRate !== null && $maxRate !== null) {
                    $query->where(function ($q) use ($minRate, $maxRate) {
                        $q->whereBetween('hourly_rate', [$minRate, $maxRate])
                          ->orWhereBetween('fixed_rate', [$minRate, $maxRate]);
                    });
                } elseif ($minRate !== null) {
                    $query->where(function ($q) use ($minRate) {
                        $q->where('hourly_rate', '>=', $minRate)
                          ->orWhere('fixed_rate', '>=', $minRate);
                    });
                } else {
                    $query->where(function ($q) use ($maxRate) {
                        $q->where('hourly_rate', '<=', $maxRate)
                          ->orWhere('fixed_rate', '<=', $maxRate);
                    });
                }
            });
        }

        return $this;
    }

    /**
     * Scope by timezone
     */
    public function byTimezone(string $timezone): static
    {
        return $this->where('required_timezone', $timezone);
    }

    /**
     * Scope by contract type
     */
    public function byContractType(string $contractType): static
    {
        return $this->where('contract_type', $contractType);
    }

    /**
     * Full-text search scope
     */
    public function search(string $searchTerm): static
    {
        return $this->where(function ($query) use ($searchTerm) {
            $query->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%")
                  ->orWhere('requirements', 'like', "%{$searchTerm}%")
                  ->orWhere('benefits', 'like', "%{$searchTerm}%");
        });
    }

    /**
     * Scope for jobs with applications count
     */
    public function withApplicationsCount(): static
    {
        return $this->withCount('applications');
    }

    /**
     * Scope for jobs posted by a specific user
     */
    public function byUser(int $userId): static
    {
        return $this->where('user_id', $userId);
    }

    /**
     * Scope for jobs posted this month
     */
    public function thisMonth(): static
    {
        return $this->whereMonth('created_at', now()->month)
                   ->whereYear('created_at', now()->year);
    }

    /**
     * Scope for jobs with views count
     */
    public function withViewsCount(): static
    {
        return $this->addSelect(['views']);
    }

    /**
     * Scope for popular jobs (high views)
     */
    public function popular(int $minViews = 10): static
    {
        return $this->where('views', '>=', $minViews);
    }

    /**
     * Apply multiple filters at once
     */
    public function applyFilters(array $filters): static
    {
        if (!empty($filters['market'])) {
            $this->byMarket($filters['market']);
        }

        if (!empty($filters['experience_level'])) {
            $this->byExperienceLevel($filters['experience_level']);
        }

        if (!empty($filters['rate_type'])) {
            $this->byRateType($filters['rate_type']);
        }

        if (!empty($filters['contract_type'])) {
            $this->byContractType($filters['contract_type']);
        }

        if (!empty($filters['timezone'])) {
            $this->byTimezone($filters['timezone']);
        }

        if (!empty($filters['search'])) {
            $this->search($filters['search']);
        }

        $this->byRateRange(
            $filters['min_rate'] ?? null,
            $filters['max_rate'] ?? null
        );

        return $this;
    }
}
