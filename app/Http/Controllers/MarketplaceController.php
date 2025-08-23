<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JobPost;
use App\Models\UserType;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\JobApplication;
use App\Models\Message;
use App\Models\Conversation;
use App\Models\Contract;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MarketplaceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'jobs', 'jobShow', 'profiles', 'profileShow', 'profileReviews']);
    }

    public function dashboard()
    {
        $user = Auth::user();
        
        // Get user's subscription stats - handle case where service doesn't exist
        $subscriptionStats = [];
        try {
            $subscriptionService = app(\App\Services\SubscriptionService::class);
            $subscriptionStats = $subscriptionService->getSubscriptionStats($user);
        } catch (\Exception $e) {
            // If service doesn't exist, provide default stats
            $subscriptionStats = [
                'plan_name' => 'Free',
                'job_posts_limit' => 0,
                'job_posts_used' => 0,
                'features' => []
            ];
        }
        
        // Get user's activity stats
        $stats = [
            'jobs_posted' => $user->jobPosts()->count(),
            'jobs_posted_this_month' => $user->jobPosts()->whereMonth('created_at', now()->month)->count(),
            'active_jobs' => $user->jobPosts()->where('status', 'active')->count(),
            'applications_sent' => $user->jobApplications()->count(),
            'applications_sent_this_month' => $user->jobApplications()->whereMonth('created_at', now()->month)->count(),
            'applications_received' => $user->jobPosts()->withCount('applications')->get()->sum('applications_count'),
            'unread_messages' => $this->getUnreadMessagesCount($user),
            'average_rating' => $user->ratingsReceived()->avg('overall_rating') ?? 0,
            'total_ratings' => $user->ratingsReceived()->count(),
            'total_reviews' => $user->ratingsReceived()->count(),
            'profile_views' => $user->userProfile ? $user->userProfile->views ?? 0 : 0,
            'earnings_this_month' => $this->getEarningsThisMonth($user),
        ];
        
        // Get recent jobs (for agencies)
        $recentJobs = $user->jobPosts()
            ->with(['applications'])
            ->latest()
            ->limit(5)
            ->get();
        
        // Get recent applications (for chatters)
        $recentApplications = $user->jobApplications()
            ->with(['jobPost', 'jobPost.user'])
            ->latest()
            ->limit(5)
            ->get();
        
        // Get recent contracts (for both agencies and chatters)
        $recentContracts = Contract::where(function($query) use ($user) {
            $query->where('employer_id', $user->id)
                  ->orWhere('contractor_id', $user->id);
        })
        ->with(['employer', 'contractor'])
        ->latest()
        ->limit(5)
        ->get();
        
        // Get featured jobs for dashboard
        $featuredJobs = JobPost::where('status', 'active')
            ->where('expires_at', '>', now())
            ->with(['user', 'user.userType', 'applications'])
            ->latest()
            ->limit(6)
            ->get();
        
        // Simplified messages - just an empty collection for now
        $recentMessages = collect();
        
        // Alias for compatibility with the blade template
        $myApplications = $recentApplications;
        
        return view('marketplace.dashboard', compact(
            'user', 'stats', 'subscriptionStats', 'recentJobs', 'recentApplications', 'myApplications', 'recentMessages', 'featuredJobs', 'recentContracts'
        ));
    }

    public function index()
    {
        $featuredJobs = JobPost::where('status', 'active')
            ->where('expires_at', '>', now())
            ->with(['user', 'user.userType'])
            ->latest()
            ->limit(6)
            ->get();
        
        $recentJobs = JobPost::where('status', 'active')
            ->where('expires_at', '>', now())
            ->with(['user', 'user.userType'])
            ->latest()
            ->limit(12)
            ->get();
        
        $userTypes = UserType::all();
        
        $stats = [
            'total_jobs' => JobPost::where('status', 'active')->count(),
            'total_chatters' => User::whereHas('userType', function($query) {
                $query->where('name', 'chatter');
            })->count(),
            'total_agencies' => User::whereHas('userType', function($query) {
                $query->whereIn('name', ['ofm_agency', 'chatting_agency']);
            })->count(),
            'jobs_filled' => JobPost::where('status', 'closed')->count()
        ];
        
        return view('theme::marketplace.index', compact('featuredJobs', 'recentJobs', 'userTypes', 'stats'));
    }

    public function jobs(Request $request)
    {
        $query = JobPost::where('status', 'active')
            ->where('expires_at', '>', now())
            ->with(['user', 'user.userType', 'applications']);
        
        // Apply filters
        if ($request->filled('market')) {
            $query->where('market', $request->market);
        }
        
        if ($request->filled('experience_level')) {
            $query->where('experience_level', $request->experience_level);
        }
        
        if ($request->filled('rate_type')) {
            $query->where('rate_type', $request->rate_type);
        }
        
        if ($request->filled('contract_type')) {
            $query->where('contract_type', $request->contract_type);
        }
        
        if ($request->filled('min_rate')) {
            $query->where(function($q) use ($request) {
                $q->where('hourly_rate', '>=', $request->min_rate)
                  ->orWhere('fixed_rate', '>=', $request->min_rate);
            });
        }
        
        if ($request->filled('max_rate')) {
            $query->where(function($q) use ($request) {
                $q->where('hourly_rate', '<=', $request->max_rate)
                  ->orWhere('fixed_rate', '<=', $request->max_rate);
            });
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('requirements', 'like', "%{$search}%")
                  ->orWhere('benefits', 'like', "%{$search}%");
            });
        }
        
        // Timezone filter - show only jobs with exact timezone match when filtering
        if ($request->filled('timezone')) {
            $query->where('required_timezone', $request->timezone);
        }

        // Order by featured first, then by creation date
        $jobs = $query->orderBy('is_featured', 'desc')
                      ->orderBy('is_urgent', 'desc')
                      ->orderBy('created_at', 'desc')
                      ->paginate(18);
        
        // Append request parameters to pagination links
        $jobs->appends($request->query());
        
        return view('theme::marketplace.jobs.index', compact('jobs'));
    }

    public function jobShow(JobPost $job)
    {
        $job->load(['user', 'user.userType', 'user.userProfile', 'applications']);
        
        // Check if user has already applied
        $hasApplied = false;
        if (Auth::check()) {
            $hasApplied = JobApplication::where('job_post_id', $job->id)
                ->where('user_id', Auth::id())
                ->exists();
        }
        
        return view('theme::marketplace.jobs.show', compact('job', 'hasApplied'));
    }

    public function profiles(Request $request)
    {
        $query = UserProfile::with(['user', 'user.userType', 'user.contractReviewsReceived', 'user.kycVerification'])
            ->where('is_active', true)
            ->whereHas('user', function($userQuery) {
                // Email verification is required for all
                $userQuery->whereNotNull('email_verified_at');

                // KYC verification logic: only required for user types that need it
                $userQuery->where(function($q) {
                    $q->where(function($q2) {
                        // Users whose type requires KYC must have it approved
                        $q2->whereHas('userType', function($typeQuery) {
                               $typeQuery->where('requires_kyc', true);
                           })
                           ->whereHas('kycVerification', function($kycQuery) {
                               $kycQuery->where('status', 'approved');
                           });
                    })->orWhere(function($q3) {
                        // Users whose type doesn't require KYC are exempt
                        $q3->whereHas('userType', function($typeQuery) {
                               $typeQuery->where('requires_kyc', false);
                           });
                    });
                });
            })
            ->whereHas('user.userType', function($q) {
                $q->whereNotIn('name', ['ofm_agency', 'chatting_agency', 'agency']);
            });
        
        // Apply filters
        if ($request->filled('user_type')) {
            $query->whereHas('user.userType', function($q) use ($request) {
                $q->where('name', $request->user_type);
            });
        }
        
        if ($request->filled('location')) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('bio', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%");
                  })
                  ->orWhere('skills', 'like', "%{$search}%")
                  ->orWhere('services', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%");
            });
        }
        
        // Timezone filter
        if ($request->filled('timezone')) {
            $query->where(function($q) use ($request) {
                $q->where('timezone', $request->timezone)
                  ->orWhere('availability_timezone', $request->timezone)
                  ->orWhereHas('user', function($userQuery) use ($request) {
                      $userQuery->where('timezone', $request->timezone);
                  });
            });
        }
        
        // Get profiles first without pagination to calculate real stats
        $allProfiles = $query->get();
        
        // Calculate real-time statistics for each profile
        foreach ($allProfiles as $profile) {
            $stats = $this->calculateProfileStats($profile->user);
            $profile->calculated_average_rating = $stats['average_rating'];
            $profile->calculated_total_reviews = $stats['total_reviews'];
            $profile->calculated_jobs_completed = $stats['jobs_completed'];
            $profile->calculated_response_time = $stats['response_time'];
        }
        
        // Apply rating filter using calculated ratings
        if ($request->filled('min_rating')) {
            $allProfiles = $allProfiles->filter(function($profile) use ($request) {
                return $profile->calculated_average_rating >= $request->min_rating;
            });
        }
        
        // Apply availability filter
        if ($request->filled('availability')) {
            $allProfiles = $allProfiles->filter(function($profile) use ($request) {
                if ($request->availability === 'available') {
                    return $profile->is_available;
                } elseif ($request->availability === 'busy') {
                    return !$profile->is_available;
                }
                return true;
            });
        }
        
        // Sort by featured status FIRST (always), then by user's selected sort option
        $sortBy = $request->get('sort', 'featured');
        
        // First, let's separate featured and non-featured profiles explicitly
        // Since we're working with UserProfile models, check the featured status directly
        $featuredProfiles = $allProfiles->filter(function($profile) {
            return $profile->is_featured && 
                   $profile->featured_until && 
                   $profile->featured_until->isFuture();
        });
        
        $nonFeaturedProfiles = $allProfiles->filter(function($profile) {
            return !($profile->is_featured && 
                     $profile->featured_until && 
                     $profile->featured_until->isFuture());
        });
        
        // Create secondary sort criteria based on user selection
        $secondarySortCriteria = [];
        switch ($sortBy) {
            case 'rating':
                $secondarySortCriteria = [
                    ['calculated_average_rating', 'desc'],
                    ['calculated_total_reviews', 'desc']
                ];
                break;
                
            case 'reviews':
                $secondarySortCriteria = [
                    ['calculated_total_reviews', 'desc'],
                    ['calculated_average_rating', 'desc']
                ];
                break;
                
            case 'recent':
                $secondarySortCriteria = [['updated_at', 'desc']];
                break;
                
            case 'price_low':
                $secondarySortCriteria = [
                    function($profile) {
                        return $profile->hourly_rate ? $profile->hourly_rate : 9999;
                    }
                ];
                break;
                
            case 'price_high':
                $secondarySortCriteria = [
                    function($profile) {
                        return $profile->hourly_rate ? -$profile->hourly_rate : -1;
                    }
                ];
                break;
                
            case 'featured':
            default:
                // Default sorting: availability, rating, and recent activity
                $secondarySortCriteria = [
                    ['is_available', 'desc'],
                    ['calculated_average_rating', 'desc'],
                    ['updated_at', 'desc']
                ];
                break;
        }
        
        // Sort featured profiles by secondary criteria
        if ($featuredProfiles->isNotEmpty()) {
            $featuredProfiles = $featuredProfiles->sortBy($secondarySortCriteria);
        }
        
        // Sort non-featured profiles by secondary criteria
        if ($nonFeaturedProfiles->isNotEmpty()) {
            $nonFeaturedProfiles = $nonFeaturedProfiles->sortBy($secondarySortCriteria);
        }
        
        // Combine: Featured profiles FIRST, then non-featured profiles
        $allProfiles = $featuredProfiles->concat($nonFeaturedProfiles);
        
        // Manual pagination
        $perPage = $request->get('per_page', 12);
        $currentPage = request()->get('page', 1);
        $offset = ($currentPage - 1) * $perPage;
        $paginatedProfiles = $allProfiles->slice($offset, $perPage);
        
        // Create paginator instance
        $profiles = new \Illuminate\Pagination\LengthAwarePaginator(
            $paginatedProfiles,
            $allProfiles->count(),
            $perPage,
            $currentPage,
            [
                'path' => request()->url(),
                'pageName' => 'page'
            ]
        );
        
        // Append request parameters to pagination links
        $profiles->appends($request->query());
        
        // Get user types for filter dropdown (exclude agencies)
        $userTypes = UserType::where('active', true)
            ->whereNotIn('name', ['ofm_agency', 'chatting_agency', 'agency'])
            ->orderBy('display_name')
            ->get();
        
        return view('theme::marketplace.profiles.index', compact('profiles', 'userTypes'));
    }

    public function profileShow(User $user)
    {
        $profile = $user->userProfile;
        if (!$profile) {
            abort(404, 'Profile not found');
        }
        
        $user->load(['userType', 'ratingsReceived.rater', 'contractReviewsReceived.reviewer', 'jobPosts' => function($query) {
            $query->where('status', 'active')->orderBy('created_at', 'desc')->limit(5);
        }]);
        
        // Get public marketplace reviews received by this user (for profile display)
        // Using the ReviewHelper trait method to ensure consistent logic
        $reviews = $user->getProfileReviews(10);
        
        if (!$reviews) {
            $reviews = collect();
        }
        
        // Calculate comprehensive profile statistics
        $stats = $this->calculateProfileStats($user);
        
        // Increment view count if not viewing own profile
        if (Auth::id() !== $user->id) {
            $profile->increment('views');
        }
        
        return view('theme::marketplace.profiles.show', compact('user', 'profile', 'reviews', 'stats'));
    }
    
    public function profileReviews(User $user)
    {
        $profile = $user->userProfile;
        if (!$profile) {
            abort(404, 'Profile not found');
        }
        
        $user->load(['userType']);
        
        // Get all public reviews received by this user (for dedicated reviews page)
        $reviews = $user->getProfileReviews(20);
        
        return view('theme::marketplace.profiles.reviews', compact('user', 'profile', 'reviews'));
    }

    public function myJobs()
    {
        $jobs = JobPost::where('user_id', Auth::id())
            ->with(['applications.user', 'applications.user.userType'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('marketplace.jobs.my-jobs', compact('jobs'));
    }

    public function myApplications()
    {
        $applications = JobApplication::where('user_id', Auth::id())
            ->with(['jobPost', 'jobPost.user', 'jobPost.user.userType'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('marketplace.applications.index', compact('applications'));
    }
    
    public function withdrawApplication(JobApplication $application)
    {
        // Only the applicant can withdraw their application
        if ($application->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        // Only allow withdrawal if status is pending
        if ($application->status !== 'pending') {
            return response()->json(['error' => 'Cannot withdraw application with current status'], 400);
        }
        
        $jobPost = $application->jobPost;
        $application->update(['status' => 'withdrawn']);
        
        // Update job post application count
        $jobPost->decrement('current_applications');
        
        return response()->json(['message' => 'Application withdrawn successfully']);
    }
    
    // Static Pages
    public function about()
    {
        return view('theme::about');
    }
    
    public function ourStory()
    {
        return view('theme::our-story');
    }
    
    public function company()
    {
        return view('theme::company');
    }
    
    public function ourTeam()
    {
        return view('theme::our-team');
    }
    
    public function workWithUs()
    {
        return view('theme::work-with-us');
    }

    public function messages(Request $request)
    {
        try {
            $userId = Auth::id();
            $user = Auth::user();
            
            // Initialize empty collections as fallback
            $conversations = collect();
            $folders = collect();
            $selectedConversation = null;
            $messages = collect();
            
            // Try to get conversation data, but handle any errors gracefully
            try {
                // Get unique conversation partners using a safer query
                $conversationUserIds = collect();
                
                // Get users who sent messages to current user
                $senders = Message::where('recipient_id', $userId)
                    ->distinct()
                    ->pluck('sender_id')
                    ->filter();
                    
                // Get users who received messages from current user
                $recipients = Message::where('sender_id', $userId)
                    ->distinct()
                    ->pluck('recipient_id')
                    ->filter();
                    
                $conversationUserIds = $senders->merge($recipients)->unique()->reject(function($id) use ($userId) {
                    return $id == $userId;
                });
                
                // Build conversation objects
                foreach ($conversationUserIds as $contactId) {
                    $contact = User::with('userType')->find($contactId);
                    if ($contact) {
                        $conversations->push((object) [
                            'id' => $contactId,
                            'otherParticipant' => $contact,
                            'updated_at' => now(),
                            'unread_count' => 0,
                            'lastMessage' => null
                        ]);
                    }
                }
                
                // Handle conversation selection
                if ($request->has('conversation')) {
                    $selectedConversation = $conversations->where('id', $request->conversation)->first();
                    if ($selectedConversation) {
                        $messages = Message::where(function($query) use ($userId, $selectedConversation) {
                            $query->where('sender_id', $userId)->where('recipient_id', $selectedConversation->id);
                        })->orWhere(function($query) use ($userId, $selectedConversation) {
                            $query->where('sender_id', $selectedConversation->id)->where('recipient_id', $userId);
                        })->orderBy('created_at', 'asc')->get();
                    }
                }
                
            } catch (\Exception $e) {
                \Log::error('Messages loading error: ' . $e->getMessage());
                // Keep empty collections as fallback
            }
            
            // Return the new Vue 3 messaging interface
            return view('messages.index', compact('conversations', 'selectedConversation', 'messages', 'folders'));
            
        } catch (\Exception $e) {
            \Log::error('Messages route error: ' . $e->getMessage());
            
            // Return safe fallback view with empty data
            $conversations = collect();
            $folders = collect();
            $selectedConversation = null;
            $messages = collect();
            
            return view('messages.index', compact('conversations', 'selectedConversation', 'messages', 'folders'));
        }
    }
    
    public function showConversation($conversationId)
    {
        return redirect()->route('messages.index', ['conversation' => $conversationId]);
    }
    
    public function storeMessage(Request $request)
    {
        $request->validate([
            'content' => 'required|string|max:2000',
            'conversation_id' => 'required|exists:users,id',
            'attachment' => 'nullable|file|max:10240',
            'job_post_id' => 'nullable|exists:job_posts,id'
        ]);
        
        $attachments = [];
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $attachmentPath = $file->store('message-attachments', 'public');
            $attachments = [[
                'name' => $file->getClientOriginalName(),
                'path' => $attachmentPath,
                'size' => $file->getSize(),
                'type' => $file->getMimeType(),
            ]];
        }
        
        $message = Message::create([
            'sender_id' => Auth::id(),
            'recipient_id' => $request->conversation_id,
            'message_content' => $request->content,
            'attachments' => $attachments,
            'message_type' => !empty($attachments) ? 'file' : 'text',
            'is_read' => false,
            'job_post_id' => $request->job_post_id
        ]);
        
        // Get recipient name for success message
        $recipient = User::find($request->conversation_id);
        $recipientName = $recipient ? $recipient->name : 'User';
        
        // Check if request came from profiles page
        $referer = $request->headers->get('referer');
        if ($referer && str_contains($referer, '/marketplace/profiles')) {
            return redirect()->route('marketplace.profiles')
                ->with('success', "Message sent successfully to {$recipientName}!");
        }
        
        return redirect()->route('messages.index', ['conversation' => $request->conversation_id])
            ->with('success', 'Message sent successfully!');
    }
    
    public function createMessage(Request $request, ?User $user = null)
    {
        $users = User::where('id', '!=', Auth::id())
            ->with('userType')
            ->limit(50)
            ->get();
        
        // Check if there's a job context
        $job = null;
        if ($request->has('job_id') && $user) {
            $job = JobPost::findOrFail($request->get('job_id'));
            // Verify the recipient owns this job
            if ($job->user_id != $user->id) {
                return redirect()->back()->with('error', 'Invalid job reference.');
            }
        }
            
        return view('messages.create', compact('users', 'user', 'job'));
    }

    public function profile()
    {
        $profile = UserProfile::where('user_id', Auth::id())->first();
        $userTypes = UserType::all();
        
        return view('marketplace.profile.edit', compact('profile', 'userTypes'));
    }
    
    public function analytics()
    {
        $stats = [
            'total_jobs' => JobPost::count(),
            'active_jobs' => JobPost::where('status', 'active')->count(),
            'total_applications' => JobApplication::count(),
            'success_rate' => $this->calculateSuccessRate(),
            'avg_response_time' => $this->calculateAvgResponseTime(),
            'top_markets' => $this->getTopMarkets(),
            'earnings_trend' => $this->getEarningsTrend(),
            'user_growth' => $this->getUserGrowth()
        ];
        
        return view('platform.analytics', compact('stats'));
    }
    
    public function tools()
    {
        return view('platform.tools');
    }
    
    public function automation()
    {
        return view('platform.automation');
    }
    
    public function integrations()
    {
        return view('platform.integrations');
    }
    
    public function api()
    {
        return view('platform.api');
    }
    
    private function calculateSuccessRate()
    {
        $totalApplications = JobApplication::count();
        $successfulApplications = JobApplication::where('status', 'hired')->count();
        
        return $totalApplications > 0 ? round(($successfulApplications / $totalApplications) * 100, 2) : 0;
    }
    
    private function calculateAvgResponseTime()
    {
        // This would calculate average response time for messages
        // For now, return a mock value
        return 2.5; // hours
    }
    
    private function getTopMarkets()
    {
        return JobPost::select('market', DB::raw('count(*) as total'))
            ->groupBy('market')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();
    }
    
    private function getEarningsTrend()
    {
        // Mock data for earnings trend over last 6 months
        return [
            ['month' => 'Jan', 'earnings' => 15000],
            ['month' => 'Feb', 'earnings' => 18000],
            ['month' => 'Mar', 'earnings' => 22000],
            ['month' => 'Apr', 'earnings' => 25000],
            ['month' => 'May', 'earnings' => 28000],
            ['month' => 'Jun', 'earnings' => 32000],
        ];
    }
    
    private function getUserGrowth()
    {
        return User::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as count')
        )
        ->where('created_at', '>=', now()->subDays(30))
        ->groupBy('date')
        ->orderBy('date')
        ->get();
    }
    
    private function getUnreadMessagesCount(User $user)
    {
        return $user->receivedMessages()->where('is_read', false)->count();
    }
    
    private function getEarningsThisMonth(User $user)
    {
        // Mock data for now - this would calculate real earnings
        return 0;
    }
    
    private function calculateProfileStats(User $user)
    {
        // Calculate average rating from contract reviews
        $averageRating = $user->contractReviewsReceived()->avg('rating') ?? 0;
        
        // Count total reviews from contract reviews
        $totalReviews = $user->contractReviewsReceived()->count();
        
        // Count jobs completed - this would be contracts that are marked as completed
        $jobsCompleted = 0;
        try {
            // Try to get completed contracts from EmploymentContract model
            if (class_exists('\App\Models\EmploymentContract')) {
                $jobsCompleted = $user->chatterContracts()
                    ->where('status', 'completed')
                    ->count();
                    
                // Also count as agency if user is agency type
                if ($user->isAgency()) {
                    $jobsCompleted += $user->agencyContracts()
                        ->where('status', 'completed')
                        ->count();
                }
            }
        } catch (\Exception $e) {
            // If EmploymentContract doesn't exist or there's an error, try Contract model
            try {
                $jobsCompleted = Contract::where(function($query) use ($user) {
                    $query->where('employer_id', $user->id)
                          ->orWhere('contractor_id', $user->id);
                })
                ->where('status', 'completed')
                ->count();
            } catch (\Exception $e) {
                // If neither model works, keep at 0
                $jobsCompleted = 0;
            }
        }
        
        // Calculate response time based on message patterns
        $responseTime = $this->calculateResponseTime($user);
        
        // Calculate profile completeness
        $profileCompleteness = $this->calculateProfileCompleteness($user);
        
        return [
            'average_rating' => round($averageRating, 1),
            'total_reviews' => $totalReviews,
            'jobs_completed' => $jobsCompleted,
            'response_time' => $responseTime,
            'profile_views' => $user->userProfile->views ?? 0,
            'member_since' => $user->created_at,
            'last_seen' => $user->last_seen_at,
            'is_online' => $user->last_seen_at && $user->last_seen_at->diffInMinutes() < 10,
            'profile_complete' => $profileCompleteness['is_complete'],
            'profile_completeness_percentage' => $profileCompleteness['percentage']
        ];
    }
    
    private function calculateResponseTime(User $user)
    {
        try {
            // Get user's messages where they replied to someone
            $conversations = Message::select('sender_id', 'recipient_id')
                ->where(function($query) use ($user) {
                    $query->where('sender_id', $user->id)
                          ->orWhere('recipient_id', $user->id);
                })
                ->distinct()
                ->get();
                
            $responseTimes = [];
            
            foreach ($conversations as $conversation) {
                $otherId = $conversation->sender_id == $user->id ? $conversation->recipient_id : $conversation->sender_id;
                
                // Get messages in this conversation ordered by time
                $messages = Message::where(function($query) use ($user, $otherId) {
                    $query->where('sender_id', $user->id)->where('recipient_id', $otherId);
                })->orWhere(function($query) use ($user, $otherId) {
                    $query->where('sender_id', $otherId)->where('recipient_id', $user->id);
                })
                ->orderBy('created_at')
                ->limit(20) // Limit to recent messages for performance
                ->get();
                
                // Find response times
                for ($i = 1; $i < $messages->count(); $i++) {
                    $previousMessage = $messages[$i - 1];
                    $currentMessage = $messages[$i];
                    
                    // If previous was from other user and current is from this user, that's a response
                    if ($previousMessage->sender_id == $otherId && $currentMessage->sender_id == $user->id) {
                        $responseTime = $currentMessage->created_at->diffInMinutes($previousMessage->created_at);
                        if ($responseTime < 1440) { // Only count responses within 24 hours
                            $responseTimes[] = $responseTime;
                        }
                    }
                }
            }
            
            if (count($responseTimes) > 0) {
                $avgMinutes = array_sum($responseTimes) / count($responseTimes);
                if ($avgMinutes < 60) {
                    return round($avgMinutes) . ' min';
                } else {
                    return round($avgMinutes / 60, 1) . ' hrs';
                }
            }
            
            return 'N/A';
        } catch (\Exception $e) {
            return 'N/A';
        }
    }
    
    private function calculateProfileCompleteness(User $user)
    {
        $profile = $user->userProfile;
        if (!$profile) {
            return ['is_complete' => false, 'percentage' => 0];
        }
        
        $requiredFields = [
            'bio' => !empty($profile->bio),
            'location' => !empty($profile->location),
            'skills' => !empty($profile->skills),
            'experience_years' => !empty($profile->experience_years),
            'hourly_rate' => !empty($profile->hourly_rate),
            'services' => !empty($profile->services),
        ];
        
        // Optional but recommended fields
        $optionalFields = [
            'languages' => !empty($profile->languages),
            'portfolio_url' => !empty($profile->portfolio_url),
            'phone' => !empty($profile->phone),
            'website' => !empty($profile->website),
        ];
        
        $completedRequired = array_sum($requiredFields);
        $totalRequired = count($requiredFields);
        $completedOptional = array_sum($optionalFields);
        $totalOptional = count($optionalFields);
        
        // Calculate percentage (required fields are weighted more heavily)
        $requiredWeight = 0.8;
        $optionalWeight = 0.2;
        
        $requiredPercentage = $totalRequired > 0 ? ($completedRequired / $totalRequired) : 0;
        $optionalPercentage = $totalOptional > 0 ? ($completedOptional / $totalOptional) : 0;
        
        $overallPercentage = ($requiredPercentage * $requiredWeight) + ($optionalPercentage * $optionalWeight);
        $percentage = round($overallPercentage * 100);
        
        // Profile is considered complete if all required fields are filled
        $isComplete = $completedRequired === $totalRequired;
        
        return ['is_complete' => $isComplete, 'percentage' => $percentage];
    }
    
    public function jobPostingRestricted()
    {
        $user = Auth::user();
        $userType = session('user_type', $user->userType?->display_name ?? 'User');
        $canApply = session('can_apply', false);
        
        return view('theme::marketplace.job-posting-restricted', compact('user', 'userType', 'canApply'));
    }
}
