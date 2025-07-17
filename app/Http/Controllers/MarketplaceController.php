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
        $this->middleware('auth')->except(['index']);
    }

    public function dashboard()
    {
        $user = Auth::user();
        
        // Get user's subscription stats
        $subscriptionService = app(\App\Services\SubscriptionService::class);
        $subscriptionStats = $subscriptionService->getSubscriptionStats($user);
        
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
        
        return view('marketplace.dashboard', compact(
            'user', 'stats', 'subscriptionStats', 'recentJobs', 'recentApplications', 'recentMessages', 'featuredJobs', 'recentContracts'
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
        if ($request->has('market')) {
            $query->where('market', $request->market);
        }
        
        if ($request->has('experience_level')) {
            $query->where('experience_level', $request->experience_level);
        }
        
        if ($request->has('rate_type')) {
            $query->where('rate_type', $request->rate_type);
        }
        
        if ($request->has('contract_type')) {
            $query->where('contract_type', $request->contract_type);
        }
        
        if ($request->has('min_rate')) {
            $query->where('hourly_rate', '>=', $request->min_rate);
        }
        
        if ($request->has('max_rate')) {
            $query->where('hourly_rate', '<=', $request->max_rate);
        }
        
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('requirements', 'like', "%{$search}%");
            });
        }
        
        $jobs = $query->orderBy('created_at', 'desc')
                      ->paginate(20);
        
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
        $query = UserProfile::with(['user', 'user.userType', 'user.ratingsReceived'])
            ->where('is_active', true)
            ->where('is_verified', true);
        
        // Apply filters
        if ($request->has('user_type')) {
            $query->whereHas('user.userType', function($q) use ($request) {
                $q->where('name', $request->user_type);
            });
        }
        
        if ($request->has('location')) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }
        
        if ($request->has('min_rating')) {
            $query->where('average_rating', '>=', $request->min_rating);
        }
        
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('bio', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%");
                  })
                  ->orWhere('skills', 'like', "%{$search}%")
                  ->orWhere('services', 'like', "%{$search}%");
            });
        }
        
        $profiles = $query->latest()->paginate(20);
        
return view('theme::marketplace.profiles.index', compact('profiles'));
    }

    public function profileShow(User $user)
    {
        $profile = $user->userProfile;
        if (!$profile) {
            abort(404, 'Profile not found');
        }
        
        $user->load(['userType', 'ratingsReceived.rater', 'jobPosts' => function($query) {
            $query->where('status', 'active')->orderBy('created_at', 'desc')->limit(5);
        }]);
        
        // Get reviews with pagination
        $reviews = $user->ratingsReceived()->with('rater')->paginate(10);
        
        // Increment view count if not viewing own profile
        if (Auth::id() !== $user->id) {
            $profile->increment('views');
        }
        
        return view('marketplace.profiles.show', compact('user', 'profile', 'reviews'));
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
        $userId = Auth::id();
        $selectedConversation = null;
        $messages = collect();
        
        // Get conversations - using Message model since we don't have Conversation model
        $conversations = collect();
        
        $conversationData = Message::select(
            DB::raw('CASE 
                WHEN sender_id = ' . $userId . ' THEN recipient_id 
                ELSE sender_id 
            END as contact_id'),
            DB::raw('MAX(created_at) as last_message_time'),
            DB::raw('COUNT(CASE WHEN recipient_id = ' . $userId . ' AND is_read = false THEN 1 END) as unread_count')
        )
        ->where(function($query) use ($userId) {
            $query->where('sender_id', $userId)->orWhere('recipient_id', $userId);
        })
        ->groupBy('contact_id')
        ->orderBy('last_message_time', 'desc')
        ->get();
        
        foreach ($conversationData as $data) {
            $contact = User::with('userType')->find($data->contact_id);
            if ($contact) {
                $latestMessage = Message::where(function($query) use ($userId, $data) {
                    $query->where('sender_id', $userId)->where('recipient_id', $data->contact_id);
                })->orWhere(function($query) use ($userId, $data) {
                    $query->where('sender_id', $data->contact_id)->where('recipient_id', $userId);
                })->latest()->first();
                
                $conversation = (object) [
                    'id' => $data->contact_id,
                    'otherParticipant' => $contact,
                    'updated_at' => $data->last_message_time,
                    'unread_count' => $data->unread_count,
                    'latest_message' => $latestMessage
                ];
                
                $conversations->push($conversation);
            }
        }
        
        // If showing specific conversation
        if ($request->has('conversation')) {
            $selectedConversation = $conversations->where('id', $request->conversation)->first();
            if ($selectedConversation) {
                $messages = Message::where(function($query) use ($userId, $selectedConversation) {
                    $query->where('sender_id', $userId)->where('recipient_id', $selectedConversation->id);
                })->orWhere(function($query) use ($userId, $selectedConversation) {
                    $query->where('sender_id', $selectedConversation->id)->where('recipient_id', $userId);
                })->with(['sender', 'recipient'])->orderBy('created_at', 'asc')->get();
                
                // Mark messages as read
                Message::where('sender_id', $selectedConversation->id)
                    ->where('recipient_id', $userId)
                    ->where('is_read', false)
                    ->update(['is_read' => true, 'read_at' => now()]);
            }
        }
        
        return view('theme::messages.index', compact('conversations', 'selectedConversation', 'messages'));
    }
    
    public function showConversation($conversationId)
    {
        return redirect()->route('marketplace.messages', ['conversation' => $conversationId]);
    }
    
    public function storeMessage(Request $request)
    {
        $request->validate([
            'content' => 'required|string|max:2000',
            'conversation_id' => 'required|exists:users,id',
            'attachment' => 'nullable|file|max:10240'
        ]);
        
        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('message-attachments', 'public');
        }
        
        $message = Message::create([
            'sender_id' => Auth::id(),
            'recipient_id' => $request->conversation_id,
            'message_content' => $request->content,
            'attachments' => $attachmentPath ? [$attachmentPath] : null,
            'message_type' => 'text',
            'is_read' => false
        ]);
        
        return redirect()->route('marketplace.messages', ['conversation' => $request->conversation_id])
            ->with('success', 'Message sent successfully!');
    }
    
    public function createMessage(?User $user = null)
    {
        $users = User::where('id', '!=', Auth::id())
            ->with('userType')
            ->limit(50)
            ->get();
            
        return view('marketplace.messages.create', compact('users', 'user'));
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
}
