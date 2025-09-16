<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\JobPost;
use App\Models\UserProfile;
use App\Models\UserType;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->get('q', '');
        $type = $request->get('type', 'all');
        $filters = $request->only(['market', 'experience_level', 'rate_type', 'location', 'user_type']);

        $results = [
            'jobs' => collect(),
            'profiles' => collect(),
            'query' => $query,
            'type' => $type,
            'filters' => $filters,
        ];

        if ($query) {
            if ($type === 'all' || $type === 'jobs') {
                $results['jobs'] = $this->searchJobs($query, $filters);
            }

            if ($type === 'all' || $type === 'profiles') {
                $results['profiles'] = $this->searchProfiles($query, $filters);
            }
        }

        return view('search.index', $results);
    }

    public function suggestions(Request $request)
    {
        $query = $request->get('q', '');
        $suggestions = [];

        if (strlen($query) >= 2) {
            // Job title suggestions
            $jobTitles = JobPost::select('title')
                ->where('title', 'LIKE', "%{$query}%")
                ->where('status', 'active')
                ->distinct()
                ->limit(5)
                ->pluck('title');

            // Skills suggestions
            $skills = UserProfile::select('skills')
                ->where('skills', 'LIKE', "%{$query}%")
                ->where('is_active', true)
                ->distinct()
                ->limit(5)
                ->pluck('skills');

            // Location suggestions
            $locations = UserProfile::select('location')
                ->where('location', 'LIKE', "%{$query}%")
                ->where('is_active', true)
                ->distinct()
                ->limit(5)
                ->pluck('location');

            $suggestions = array_merge(
                $jobTitles->toArray(),
                $skills->toArray(),
                $locations->toArray()
            );
        }

        return response()->json(array_unique($suggestions));
    }

    public function globalSearch(Request $request)
    {
        $query = $request->get('q', '');
        $results = [];

        if ($query) {
            // Quick search across all content
            $results = [
                'jobs' => $this->quickSearchJobs($query),
                'profiles' => $this->quickSearchProfiles($query),
                'total' => 0,
            ];

            $results['total'] = $results['jobs']->count() + $results['profiles']->count();
        }

        return response()->json($results);
    }

    public function filters()
    {
        return response()->json([
            'markets' => JobPost::select('market')
                ->distinct()
                ->whereNotNull('market')
                ->orderBy('market')
                ->pluck('market'),
            'experience_levels' => ['beginner', 'intermediate', 'advanced'],
            'rate_types' => ['hourly', 'fixed', 'commission'],
            'contract_types' => ['full_time', 'part_time', 'contract'],
            'user_types' => UserType::orderBy('name')->pluck('name'),
            'locations' => UserProfile::select('location')
                ->distinct()
                ->whereNotNull('location')
                ->where('location', '!=', '')
                ->orderBy('location')
                ->limit(20)
                ->pluck('location'),
        ]);
    }

    private function searchJobs($query, $filters)
    {
        $jobQuery = JobPost::with(['user', 'user.userType', 'applications'])
            ->where('status', 'active')
            ->where('expires_at', '>', now());

        // Text search
        $jobQuery->where(function ($q) use ($query): void {
            $q->where('title', 'LIKE', "%{$query}%")
                ->orWhere('description', 'LIKE', "%{$query}%")
                ->orWhere('requirements', 'LIKE', "%{$query}%")
                ->orWhere('benefits', 'LIKE', "%{$query}%");
        });

        // Apply filters
        if (! empty($filters['market'])) {
            $jobQuery->where('market', $filters['market']);
        }

        if (! empty($filters['experience_level'])) {
            $jobQuery->where('experience_level', $filters['experience_level']);
        }

        if (! empty($filters['rate_type'])) {
            $jobQuery->where('rate_type', $filters['rate_type']);
        }

        return $jobQuery->orderBy('created_at', 'desc')->paginate(10);
    }

    private function searchProfiles($query, $filters)
    {
        $profileQuery = UserProfile::with(['user', 'user.userType', 'user.ratingsReceived'])
            ->where('is_active', true)
            ->where('is_verified', true);

        // Text search
        $profileQuery->where(function ($q) use ($query): void {
            $q->where('bio', 'LIKE', "%{$query}%")
                ->orWhere('skills', 'LIKE', "%{$query}%")
                ->orWhere('services', 'LIKE', "%{$query}%")
                ->orWhere('location', 'LIKE', "%{$query}%")
                ->orWhereHas('user', function ($userQuery) use ($query): void {
                    $userQuery->where('name', 'LIKE', "%{$query}%");
                });
        });

        // Apply filters
        if (! empty($filters['user_type'])) {
            $profileQuery->whereHas('user.userType', function ($q) use ($filters): void {
                $q->where('name', $filters['user_type']);
            });
        }

        if (! empty($filters['location'])) {
            $profileQuery->where('location', 'LIKE', "%{$filters['location']}%");
        }

        return $profileQuery->orderBy('average_rating', 'desc')->paginate(10);
    }

    private function quickSearchJobs($query)
    {
        return JobPost::select('id', 'title', 'market', 'hourly_rate', 'created_at')
            ->with(['user:id,name'])
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->where(function ($q) use ($query): void {
                $q->where('title', 'LIKE', "%{$query}%")
                    ->orWhere('description', 'LIKE', "%{$query}%");
            })
            ->limit(5)
            ->get();
    }

    private function quickSearchProfiles($query)
    {
        return UserProfile::select('user_id', 'bio', 'skills', 'location', 'average_rating')
            ->with(['user:id,name'])
            ->where('is_active', true)
            ->where('is_verified', true)
            ->where(function ($q) use ($query): void {
                $q->where('bio', 'LIKE', "%{$query}%")
                    ->orWhere('skills', 'LIKE', "%{$query}%")
                    ->orWhereHas('user', function ($userQuery) use ($query): void {
                        $userQuery->where('name', 'LIKE', "%{$query}%");
                    });
            })
            ->limit(5)
            ->get();
    }
}
