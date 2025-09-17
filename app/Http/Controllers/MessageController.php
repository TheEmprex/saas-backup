<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class MessageController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $conversations = collect();

        // Get all messages for this user
        $messages = Message::query()->where('sender_id', $userId)
            ->orWhere('recipient_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        if ($messages->isEmpty()) {
            return view('theme::messages.index', ['conversations' => $conversations]);
        }

        // Group messages by conversation partner
        $conversationData = [];

        foreach ($messages as $message) {
            $contactId = ($message->sender_id === $userId) ? $message->recipient_id : $message->sender_id;

            if (! isset($conversationData[$contactId])) {
                $conversationData[$contactId] = [
                    'contact_id' => $contactId,
                    'last_message_time' => $message->created_at,
                    'unread_count' => 0,
                    'last_message' => $message,
                ];
            }

            // Update last message time if this message is newer
            if ($message->created_at > $conversationData[$contactId]['last_message_time']) {
                $conversationData[$contactId]['last_message_time'] = $message->created_at;
                $conversationData[$contactId]['last_message'] = $message;
            }

            // Count unread messages
            if ($message->recipient_id === $userId && ! $message->is_read) {
                $conversationData[$contactId]['unread_count']++;
            }
        }

        // Convert to collection and sort by last message time
        foreach ($conversationData as $data) {
            $conversation = (object) $data;
            $conversation->contact = User::with(['userProfile', 'userType'])->find($conversation->contact_id);

            if ($conversation->contact) {
                $conversations->push($conversation);
            }
        }

        $conversations = $conversations->sortByDesc('last_message_time')->values();

        return view('theme::messages.index', ['conversations' => $conversations]);
    }

    public function create($recipientId)
    {
        $recipient = User::with(['userProfile', 'userType'])->findOrFail($recipientId);

        // Check if user can message this recipient
        if (! $this->canMessage(Auth::id(), $recipientId)) {
            return redirect()->back()->with('error', 'You cannot message this user.');
        }

        return view('theme::messages.create', ['recipient' => $recipient]);
    }

    public function store(Request $request, $user)
    {
        try {
            $validated = $request->validate([
                'content' => 'required|string|max:2000',
                'subject' => 'nullable|string|max:255',
                'job_post_id' => 'nullable|exists:job_posts,id',
                'job_application_id' => 'nullable|exists:job_applications,id',
            ]);

            // Create message data
            $messageData = [
                'recipient_id' => $user,
                'sender_id' => Auth::id(),
                'message_content' => $validated['content'],
                'message_type' => 'text',
                'is_read' => false,
                'job_post_id' => $validated['job_post_id'] ?? null,
                'job_application_id' => $validated['job_application_id'] ?? null,
            ];

            // Generate thread ID for conversation grouping
            $threadId = 'thread_'.min($messageData['sender_id'], $messageData['recipient_id']).'_'.max($messageData['sender_id'], $messageData['recipient_id']);
            $messageData['thread_id'] = $threadId;

            // Check if users can message each other (basic permissions)
            if (! $this->canMessage($messageData['sender_id'], $messageData['recipient_id'])) {
                if ($request->ajax()) {
                    return response()->json(['success' => false, 'error' => 'You cannot message this user.'], 403);
                }

                return redirect()->back()->with('error', 'You cannot message this user.');
            }

            $message = Message::create($messageData);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message->load(['sender', 'recipient']),
                ]);
            }

            return redirect()->route('messages.web.show', $messageData['recipient_id'])
                ->with('success', 'Message sent successfully!');

        } catch (Exception $exception) {
            Log::error('Message store error: '.$exception->getMessage());

            if ($request->ajax()) {
                return response()->json(['success' => false, 'error' => 'Failed to send message.'], 500);
            }

            return redirect()->back()->with('error', 'Failed to send message.');
        }
    }

    public function show($contactId)
    {
        $userId = Auth::id();
        $contact = User::with(['userProfile', 'userType'])->findOrFail($contactId);

        // Fetch messages
        $messages = Message::conversation($userId, $contactId)
            ->with(['sender', 'recipient', 'jobPost'])
            ->orderBy('created_at', 'asc')
            ->paginate(50);

        // Mark as read
        Message::query()->where('sender_id', $contactId)
            ->where('recipient_id', $userId)
            ->update(['is_read' => true, 'read_at' => now()]);

        return view('theme::messages.show', ['contact' => $contact, 'messages' => $messages]);
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
        $count = Message::query()->where('recipient_id', Auth::id())
            ->where('is_read', false)
            ->count();

        return response()->json(['unread_count' => $count]);
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

        // Allow messaging between all verified users
        return $sender && $recipient;
    }
}
