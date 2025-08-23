<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MailController extends Controller
{
    // Inbox: conversations where current user participates
    public function index()
    {
        $userId = Auth::id();
        $conversations = Conversation::forUser($userId)
            ->with(['user1:id,name,username,email', 'user2:id,name,username,email', 'lastMessage'])
            ->orderByDesc('last_message_at')
            ->orderByDesc('updated_at')
            ->paginate(20);

        return view('mail.index', compact('conversations'));
    }

    // Sent mailbox: conversations where last message sender is current user
    public function sent()
    {
        $userId = Auth::id();
        $conversations = Conversation::forUser($userId)
            ->whereHas('lastMessage', function ($q) use ($userId) {
                $q->where('sender_id', $userId);
            })
            ->with(['user1:id,name,username,email', 'user2:id,name,username,email', 'lastMessage'])
            ->orderByDesc('last_message_at')
            ->paginate(20);

        return view('mail.sent', compact('conversations'));
    }

    // Compose screen
    public function compose()
    {
        return view('mail.compose');
    }

    // Show a single thread (conversation)
    public function show(Conversation $conversation)
    {
        $userId = Auth::id();
        abort_unless($conversation->hasParticipant($userId), 403);

        $messages = Message::where('conversation_id', $conversation->id)
            ->with('sender:id,name,email,username')
            ->orderBy('created_at', 'asc')
            ->paginate(30);

        return view('mail.show', compact('conversation', 'messages'));
    }

    // Send message: resolve recipient by username/email, find/create conversation, create message
    public function send(Request $request)
    {
        $data = $request->validate([
            'to' => 'required|string',
            'subject' => 'nullable|string|max:255',
            'content' => 'required|string',
        ]);

        $recipient = User::query()
            ->where('email', $data['to'])
            ->orWhere('username', $data['to'])
            ->first();

        if (!$recipient) {
            return back()->withErrors(['to' => 'Recipient not found'])->withInput();
        }

        $senderId = Auth::id();
        abort_if($recipient->id === $senderId, 403);

        // Find or create conversation between users
        if (method_exists(Conversation::class, 'findOrCreateBetweenUsers')) {
            $conversation = Conversation::findOrCreateBetweenUsers($senderId, $recipient->id);
        } else {
            // Fallback: try to locate existing conversation by pair
            $conversation = Conversation::where(function ($q) use ($senderId, $recipient) {
                $q->where('user1_id', $senderId)->where('user2_id', $recipient->id);
            })->orWhere(function ($q) use ($senderId, $recipient) {
                $q->where('user1_id', $recipient->id)->where('user2_id', $senderId);
            })->first();

            if (!$conversation) {
                $conversation = Conversation::create([
                    'user1_id' => $senderId,
                    'user2_id' => $recipient->id,
                    'last_message_at' => now(),
                ]);
            }
        }

        // Create message (no realtime, simple text-only)
        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $senderId,
            'content' => $data['content'],
            'message_type' => 'text',
            'metadata' => [ 'subject' => $data['subject'] ?? null ],
            'read_by' => [$senderId],
        ]);

        // Update conversation last message pointers if helper exists
        if (method_exists($conversation, 'updateLastMessage')) {
            $conversation->updateLastMessage($message);
        } else {
            $conversation->last_message_id = $message->id;
            $conversation->last_message_at = now();
            $conversation->save();
        }

        return redirect()->route('mail.show', $conversation)->with('status', 'Message sent');
    }
}

