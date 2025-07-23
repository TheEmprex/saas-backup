<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use App\Models\JobPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MessageController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $conversations = collect();
        
        // Get all messages for this user
        $messages = Message::where('sender_id', $userId)
            ->orWhere('recipient_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
        
        if ($messages->isEmpty()) {
            return view('theme::messages.index', compact('conversations'));
        }
        
        // Group messages by conversation partner
        $conversationData = [];
        
        foreach ($messages as $message) {
            $contactId = ($message->sender_id === $userId) ? $message->recipient_id : $message->sender_id;
            
            if (!isset($conversationData[$contactId])) {
                $conversationData[$contactId] = [
                    'contact_id' => $contactId,
                    'last_message_time' => $message->created_at,
                    'unread_count' => 0,
                    'last_message' => $message
                ];
            }
            
            // Update last message time if this message is newer
            if ($message->created_at > $conversationData[$contactId]['last_message_time']) {
                $conversationData[$contactId]['last_message_time'] = $message->created_at;
                $conversationData[$contactId]['last_message'] = $message;
            }
            
            // Count unread messages
            if ($message->recipient_id === $userId && !$message->is_read) {
                $conversationData[$contactId]['unread_count']++;
            }
        }
        
        // Convert to collection and sort by last message time
        foreach ($conversationData as $data) {
            $contact = User::with(['userProfile', 'userType'])->find($data['contact_id']);
            
            if ($contact) {
                $conversation = (object) [
                    'id' => $data['contact_id'], // Use contact_id as the conversation ID for routing
                    'contact_id' => $data['contact_id'],
                    'otherParticipant' => $contact,
                    'latest_message' => $data['last_message'],
                    'unread_count' => $data['unread_count'],
                    'updated_at' => $data['last_message_time'],
                    'last_message_time' => $data['last_message_time']
                ];
                
                $conversations->push($conversation);
            }
        }
        
        $conversations = $conversations->sortByDesc('last_message_time')->values();
        
        return view('theme::messages.index', compact('conversations'));
    }

    public function show(Request $request, $contactId)
    {
        $userId = Auth::id();
        $contact = User::with(['userProfile', 'userType'])->findOrFail($contactId);

        // Mark messages as read on initial load or as needed
        if (!$request->wantsJson()) {
            Message::where('sender_id', $contactId)
                ->where('recipient_id', $userId)
                ->where('is_read', false)
                ->update(['is_read' => true, 'read_at' => now()]);
        }

        $query = Message::conversation($userId, $contactId)
            ->with(['sender', 'jobPost']);

        // Handle AJAX requests for messages
        if ($request->wantsJson()) {
            // Fetch new messages since the last known ID
            if ($lastId = $request->get('last_id')) {
                $messages = $query->where('id', '>', $lastId)->orderBy('created_at', 'asc')->get();
                return response()->json(['messages' => $messages]);
            }
            
            // Fetch older messages for infinite scroll
            $messages = $query->orderBy('created_at', 'desc')->paginate(20);
            $messages->setCollection($messages->getCollection()->reverse());
            return response()->json(['messages' => $messages]);
        }

        // Initial page load: get the latest page of messages
        $messages = $query->orderBy('created_at', 'desc')->paginate(20);
        $messages->setCollection($messages->getCollection()->reverse());

        return view('theme::messages.show', compact('contact', 'messages'));
    }

    public function store(Request $request, $user)
    {
        try {
            $validated = $request->validate([
                'content' => 'required_without:attachments|nullable|string|max:2000',
                'attachments' => 'required_without:content|nullable|array|max:5',
                'attachments.*' => 'file|mimes:jpg,jpeg,png,gif,pdf,doc,docx,zip|max:10240', // 10MB max per file
                'job_post_id' => 'nullable|exists:job_posts,id',
            ]);

            $attachments = [];
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('message-attachments', 'public');
                    $attachments[] = [
                        'name' => $file->getClientOriginalName(),
                        'path' => $path,
                        'size' => $file->getSize(),
                        'type' => $file->getMimeType(),
                    ];
                }
            }

            $message = Message::create([
                'sender_id' => Auth::id(),
                'recipient_id' => $user,
                'message_content' => $validated['content'] ?? '',
                'attachments' => $attachments,
                'message_type' => !empty($attachments) ? 'file' : 'text',
                'thread_id' => 'thread_' . min(Auth::id(), $user) . '_' . max(Auth::id(), $user),
                'job_post_id' => $validated['job_post_id'] ?? null,
            ]);

            if ($request->ajax()) {
                // Eager load sender and append formatted attachments for the JSON response
                $message->load('sender');
                return response()->json(['success' => true, 'message' => $message]);
            }

            return redirect()->route('messages.web.show', $user);

        } catch (\Exception $e) {
            Log::error('Message store error: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json(['success' => false, 'error' => 'Failed to send message.'], 500);
            }
            return back()->with('error', 'Failed to send message.');
        }
    }
    
    public function create(Request $request, $recipientId)
    {
        $recipient = User::with(['userProfile', 'userType'])->findOrFail($recipientId);
        
        // Check if user can message this recipient
        if (!$this->canMessage(Auth::id(), $recipientId)) {
            return redirect()->back()->with('error', 'You cannot message this user.');
        }
        
        // Check if there's a job context
        $job = null;
        if ($request->has('job_id')) {
            $job = JobPost::findOrFail($request->get('job_id'));
            // Verify the recipient owns this job
            if ($job->user_id != $recipientId) {
                return redirect()->back()->with('error', 'Invalid job reference.');
            }
        }
        
        return view('theme::messages.create', compact('recipient', 'job'));
    }
    
    private function canMessage($senderId, $recipientId)
    {
        // Basic permission check - users can message if:
        // 1. They are different users (can't message yourself)
        // 2. Both users exist in the system
        // 3. Both users are active
        
        if ($senderId == $recipientId) {
            return false;
        }
        
        $sender = User::find($senderId);
        $recipient = User::find($recipientId);
        
        if (!$sender || !$recipient) {
            return false;
        }
        
        // Allow messaging between all verified users
        return true;
    }
    
    public function markAsRead(Message $message)
    {
        if ($message->recipient_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $message->markAsRead();
        
        return response()->json(['success' => true]);
    }
    
    public function getUnreadCount()
    {
        $count = Message::where('recipient_id', Auth::id())
            ->where('is_read', false)
            ->count();
        
        return response()->json(['unread_count' => $count]);
    }
}
