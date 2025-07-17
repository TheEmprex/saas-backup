<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class MessageController extends Controller
{
    /**
     * Display a listing of conversations.
     */
    public function index(Request $request)
    {
        $userId = Auth::id();
        
        // Get all conversations (unique sender/recipient pairs)
        $conversations = Message::where('sender_id', $userId)
            ->orWhere('recipient_id', $userId)
            ->with(['sender', 'recipient'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function ($message) use ($userId) {
                // Create a unique key for each conversation
                $otherUserId = $message->sender_id === $userId ? $message->recipient_id : $message->sender_id;
                return $otherUserId;
            })
            ->map(function ($messages) use ($userId) {
                $latestMessage = $messages->first();
                $otherUserId = $latestMessage->sender_id === $userId ? $latestMessage->recipient_id : $latestMessage->sender_id;
                $otherUser = User::with('userType')->find($otherUserId);
                
                // Count unread messages in this conversation
                $unreadCount = $messages->where('recipient_id', $userId)
                    ->where('is_read', false)
                    ->count();
                
                return [
                    'other_user' => $otherUser,
                    'latest_message' => $latestMessage,
                    'unread_count' => $unreadCount,
                    'updated_at' => $latestMessage->created_at,
                ];
            })
            ->sortByDesc('updated_at')
            ->values();

        return response()->json($conversations);
    }

    /**
     * Get messages for a specific conversation.
     */
    public function conversation(User $user, Request $request)
    {
        $userId = Auth::id();
        $otherUserId = $user->id;
        
        if ($userId === $otherUserId) {
            return response()->json(['error' => 'Cannot message yourself'], 400);
        }

        $messages = Message::where(function ($query) use ($userId, $otherUserId) {
                $query->where('sender_id', $userId)
                      ->where('recipient_id', $otherUserId);
            })
            ->orWhere(function ($query) use ($userId, $otherUserId) {
                $query->where('sender_id', $otherUserId)
                      ->where('recipient_id', $userId);
            })
            ->with(['sender', 'recipient'])
            ->orderBy('created_at', 'asc')
            ->paginate($request->get('per_page', 50));

        // Mark messages as read
        Message::where('sender_id', $otherUserId)
            ->where('recipient_id', $userId)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);

        return response()->json($messages);
    }

    /**
     * Store a newly created message.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'recipient_id' => 'required|exists:users,id',
            'message_content' => 'required|string|max:5000',
            'job_post_id' => 'nullable|exists:job_posts,id',
            'job_application_id' => 'nullable|exists:job_applications,id',
            'message_type' => 'in:text,file,system',
            'attachments' => 'nullable|array',
            'attachments.*' => 'string', // File paths
            'parent_message_id' => 'nullable|exists:messages,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $recipientId = $request->recipient_id;
        $senderId = Auth::id();

        if ($senderId === $recipientId) {
            return response()->json(['error' => 'Cannot message yourself'], 400);
        }

        $messageData = $validator->validated();
        $messageData['sender_id'] = $senderId;
        $messageData['message_type'] = $messageData['message_type'] ?? 'text';
        $messageData['is_read'] = false;
        
        // Generate thread ID if not exists
        if (!isset($messageData['thread_id'])) {
            $messageData['thread_id'] = $this->generateThreadId($senderId, $recipientId);
        }

        // Convert attachments to JSON
        if (isset($messageData['attachments'])) {
            $messageData['attachments'] = json_encode($messageData['attachments']);
        }

        $message = Message::create($messageData);

        return response()->json($message->load(['sender', 'recipient']), 201);
    }

    /**
     * Display the specified message.
     */
    public function show(Message $message)
    {
        $userId = Auth::id();
        
        // Check if user is sender or recipient
        if ($message->sender_id !== $userId && $message->recipient_id !== $userId) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Mark as read if user is recipient
        if ($message->recipient_id === $userId && !$message->is_read) {
            $message->update([
                'is_read' => true,
                'read_at' => now()
            ]);
        }

        return response()->json($message->load(['sender', 'recipient']));
    }

    /**
     * Update the specified message.
     */
    public function update(Request $request, Message $message)
    {
        $userId = Auth::id();
        
        // Only sender can update (edit) their message
        if ($message->sender_id !== $userId) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Only allow editing within 5 minutes
        if ($message->created_at->diffInMinutes(now()) > 5) {
            return response()->json(['error' => 'Message can only be edited within 5 minutes'], 400);
        }

        $validator = Validator::make($request->all(), [
            'message_content' => 'required|string|max:5000',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $message->update([
            'message_content' => $request->message_content,
            'updated_at' => now()
        ]);

        return response()->json($message->load(['sender', 'recipient']));
    }

    /**
     * Remove the specified message.
     */
    public function destroy(Message $message)
    {
        $userId = Auth::id();
        
        // Only sender can delete their message
        if ($message->sender_id !== $userId) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $message->delete();

        return response()->json(['message' => 'Message deleted successfully']);
    }

    /**
     * Mark message as read.
     */
    public function markAsRead(Message $message)
    {
        $userId = Auth::id();
        
        // Only recipient can mark as read
        if ($message->recipient_id !== $userId) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $message->update([
            'is_read' => true,
            'read_at' => now()
        ]);

        return response()->json(['message' => 'Message marked as read']);
    }

    /**
     * Mark all messages in a conversation as read.
     */
    public function markConversationAsRead(User $user)
    {
        $userId = Auth::id();
        $otherUserId = $user->id;

        Message::where('sender_id', $otherUserId)
            ->where('recipient_id', $userId)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);

        return response()->json(['message' => 'Conversation marked as read']);
    }

    /**
     * Get unread message count.
     */
    public function unreadCount()
    {
        $count = Message::where('recipient_id', Auth::id())
            ->where('is_read', false)
            ->count();

        return response()->json(['unread_count' => $count]);
    }

    /**
     * Search messages.
     */
    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'query' => 'required|string|min:2',
            'user_id' => 'nullable|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $userId = Auth::id();
        $query = $request->query;
        
        $messages = Message::where(function ($q) use ($userId) {
                $q->where('sender_id', $userId)
                  ->orWhere('recipient_id', $userId);
            })
            ->where('message_content', 'like', "%{$query}%")
            ->when($request->user_id, function ($q) use ($request, $userId) {
                $q->where(function ($query) use ($request, $userId) {
                    $query->where('sender_id', $userId)
                          ->where('recipient_id', $request->user_id);
                })
                ->orWhere(function ($query) use ($request, $userId) {
                    $query->where('sender_id', $request->user_id)
                          ->where('recipient_id', $userId);
                });
            })
            ->with(['sender', 'recipient'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($messages);
    }

    /**
     * Generate a consistent thread ID for two users.
     */
    private function generateThreadId($userId1, $userId2)
    {
        $ids = [$userId1, $userId2];
        sort($ids);
        return 'thread_' . md5(implode('_', $ids));
    }
}
