<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\JobApplication;
use App\Models\JobPost;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = $this->getNotifications();

        return view('notifications.index', ['notifications' => $notifications]);
    }

    public function getUnreadCount()
    {
        $count = $this->getUnreadNotificationsCount();

        return response()->json(['count' => $count]);
    }

    public function markAsRead($notificationId)
    {
        // This would mark specific notification as read
        // For now, we'll implement basic message read functionality
        return response()->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        $userId = Auth::id();

        // Mark all messages as read
        Message::where('recipient_id', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return response()->json(['success' => true]);
    }

    public function getRecentActivity()
    {
        $userId = Auth::id();
        $activities = collect();

        // Recent messages
        $recentMessages = Message::where('recipient_id', $userId)
            ->with(['sender', 'sender.userType'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        foreach ($recentMessages as $message) {
            $activities->push([
                'type' => 'message',
                'title' => 'Message from '.$message->sender->name,
                'description' => substr($message->message_content, 0, 100).'...',
                'created_at' => $message->created_at,
                'url' => route('messages.web.show', $message->sender),
                'icon' => 'message-circle',
                'color' => 'blue',
            ]);
        }

        // Recent job applications
        $recentApplications = JobApplication::where('user_id', $userId)
            ->with(['jobPost', 'jobPost.user'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        foreach ($recentApplications as $application) {
            $activities->push([
                'type' => 'application',
                'title' => 'Applied to '.$application->jobPost->title,
                'description' => 'Status: '.ucfirst($application->status),
                'created_at' => $application->created_at,
                'url' => route('jobs.show', $application->jobPost),
                'icon' => 'briefcase',
                'color' => 'green',
            ]);
        }

        return $activities->sortByDesc('created_at')->take(10);
    }

    private function getNotifications()
    {
        $userId = Auth::id();
        $notifications = collect();

        // Get unread messages
        $unreadMessages = Message::where('recipient_id', $userId)
            ->where('is_read', false)
            ->with(['sender', 'sender.userType'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        foreach ($unreadMessages as $message) {
            $notifications->push([
                'id' => 'message_'.$message->id,
                'type' => 'message',
                'title' => 'New message from '.$message->sender->name,
                'message' => substr($message->message_content, 0, 100).'...',
                'created_at' => $message->created_at,
                'read_at' => $message->read_at,
                'url' => route('messages.web.show', $message->sender),
                'icon' => 'message-circle',
            ]);
        }

        // Get job application updates
        $jobApplications = JobApplication::where('user_id', $userId)
            ->where('status', '!=', 'pending')
            ->with(['jobPost', 'jobPost.user'])
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get();

        foreach ($jobApplications as $application) {
            $status = ucfirst($application->status);
            $notifications->push([
                'id' => 'application_'.$application->id,
                'type' => 'application',
                'title' => 'Application '.$status,
                'message' => "Your application for '{$application->jobPost->title}' has been {$application->status}.",
                'created_at' => $application->updated_at,
                'read_at' => null,
                'url' => route('jobs.show', $application->jobPost),
                'icon' => $application->status === 'accepted' ? 'check-circle' : 'x-circle',
            ]);
        }

        // Get notifications for job posts (for job creators)
        $myJobPosts = JobPost::where('user_id', $userId)
            ->with(['applications' => function ($query): void {
                $query->where('status', 'pending')->latest();
            }])
            ->get();

        foreach ($myJobPosts as $jobPost) {
            foreach ($jobPost->applications as $application) {
                $notifications->push([
                    'id' => 'job_application_'.$application->id,
                    'type' => 'job_application',
                    'title' => 'New Job Application',
                    'message' => "New application received for '{$jobPost->title}'",
                    'created_at' => $application->created_at,
                    'read_at' => null,
                    'url' => route('jobs.applications', $jobPost),
                    'icon' => 'user-plus',
                ]);
            }
        }

        return $notifications->sortByDesc('created_at');
    }

    private function getUnreadNotificationsCount(): int|float
    {
        $userId = Auth::id();
        $count = 0;

        // Count unread messages
        $count += Message::where('recipient_id', $userId)
            ->where('is_read', false)
            ->count();

        // Count pending job applications (for job creators)
        $count += JobApplication::whereHas('jobPost', function ($query) use ($userId): void {
            $query->where('user_id', $userId);
        })
            ->where('status', 'pending')
            ->count();

        return $count;
    }
}
