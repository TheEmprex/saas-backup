<?php

namespace App\Traits;

use App\Models\Rating;

trait ReviewHelper
{
    /**
     * Get reviews received by this user for profile display
     * This ensures only reviews WHERE this user was rated are shown, not reviews they left
     * 
     * @param int $perPage Number of reviews per page
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getProfileReviews($perPage = 10)
    {
        return $this->ratingsReceived()
            ->where('is_public', true)
            ->with(['rater' => function ($query) {
                $query->select('id', 'name', 'avatar');
            }])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get reviews left by this user (for admin/management purposes)
     * This shows reviews WHERE this user was the rater, not the rated user
     * 
     * @param int $perPage Number of reviews per page
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getReviewsGiven($perPage = 10)
    {
        return $this->ratingsGiven()
            ->where('is_public', true)
            ->with(['rated' => function ($query) {
                $query->select('id', 'name', 'avatar');
            }])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get comprehensive review statistics
     * 
     * @return array
     */
    public function getReviewStatistics()
    {
        $receivedReviews = $this->ratingsReceived()->where('is_public', true);
        $givenReviews = $this->ratingsGiven()->where('is_public', true);
        
        return [
            'received' => [
                'count' => $receivedReviews->count(),
                'average_rating' => round($receivedReviews->avg('overall_rating') ?? 0, 1),
                'total_rating_points' => $receivedReviews->sum('overall_rating'),
                'rating_breakdown' => Rating::getRatingBreakdown($this->id)
            ],
            'given' => [
                'count' => $givenReviews->count(),
                'average_given' => round($givenReviews->avg('overall_rating') ?? 0, 1),
            ],
            'profile_rating' => [
                'average' => $this->userProfile?->average_rating ?? 0,
                'total' => $this->userProfile?->total_ratings ?? 0
            ]
        ];
    }

    /**
     * Check if user has received any reviews
     * 
     * @return bool
     */
    public function hasReceivedReviews()
    {
        return $this->ratingsReceived()->where('is_public', true)->exists();
    }

    /**
     * Check if user has left any reviews
     * 
     * @return bool
     */
    public function hasGivenReviews()
    {
        return $this->ratingsGiven()->where('is_public', true)->exists();
    }

    /**
     * Get recent reviews received by this user
     * 
     * @param int $limit Number of recent reviews to get
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRecentReceivedReviews($limit = 5)
    {
        return $this->ratingsReceived()
            ->where('is_public', true)
            ->with(['rater' => function ($query) {
                $query->select('id', 'name', 'avatar');
            }])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get reviews by rating (for filtering purposes)
     * 
     * @param int $rating Rating to filter by (1-5)
     * @param int $perPage Number of reviews per page
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getReviewsByRating($rating, $perPage = 10)
    {
        return $this->ratingsReceived()
            ->where('is_public', true)
            ->where('overall_rating', $rating)
            ->with(['rater' => function ($query) {
                $query->select('id', 'name', 'avatar');
            }])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Validate that reviews displayed on profile belong to this user as rated user
     * This is a helper method for debugging/testing
     * 
     * @return bool
     */
    public function validateProfileReviews()
    {
        $profileReviews = $this->getProfileReviews(999); // Get all reviews
        
        foreach ($profileReviews as $review) {
            // All reviews should have this user as the rated_id
            if ($review->rated_id !== $this->id) {
                return false;
            }
            
            // All reviews should NOT have this user as the rater_id
            if ($review->rater_id === $this->id) {
                return false;
            }
        }
        
        return true;
    }
}
