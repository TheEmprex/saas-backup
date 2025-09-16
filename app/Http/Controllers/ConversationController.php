<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ConversationController extends Controller
{
    /**
     * Get conversations for the authenticated user
     */
    public function index(): JsonResponse
    {
        $userId = auth()->id();
        
        $conversations = Conversation::where('user1_id', $userId)
            ->orWhere('user2_id', $userId)
            ->with(['user1:id,name,email', 'user2:id,name,email', 'lastMessage:id,content,sender_id,created_at'])
            ->orderBy('updated_at', 'desc')
            ->get();

        return response()->json($conversations);
    }

    /**
     * Create a new conversation
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $userId = auth()->id();
        $otherUserId = $request->input('user_id');

        // Prevent same user conversation
        if ($userId === (int)$otherUserId) {
            return response()->json(['error' => 'Cannot create conversation with yourself'], 400);
        }

        // Check if conversation already exists
        $conversation = Conversation::where(function($query) use ($userId, $otherUserId) {
            $query->where('user1_id', $userId)->where('user2_id', $otherUserId);
        })->orWhere(function($query) use ($userId, $otherUserId) {
            $query->where('user1_id', $otherUserId)->where('user2_id', $userId);
        })->first();

        if ($conversation) {
            return response()->json($conversation);
        }

        $conversation = Conversation::create([
            'user1_id' => $userId,
            'user2_id' => $otherUserId,
            'conversation_type' => 'direct'
        ]);

        $conversation->load(['user1:id,name,email', 'user2:id,name,email']);

        return response()->json($conversation, 201);
    }

    /**
     * Get a specific conversation
     */
    public function show($id): JsonResponse
    {
        $conversation = Conversation::with([
            'user1:id,name,email', 
            'user2:id,name,email', 
            'messages.sender:id,name,email'
        ])->findOrFail($id);
        
        // Check if user part of conversation
        $userId = auth()->id();
        if ($conversation->user1_id !== $userId && $conversation->user2_id !== $userId) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json($conversation);
    }

    /**
     * Update conversation settings
     */
    public function update(Request $request, $id): JsonResponse
    {
        $request->validate([
            'title' => 'sometimes|string|max:255',
            'is_archived' => 'sometimes|boolean'
        ]);

        $conversation = Conversation::findOrFail($id);

        // Check if user part of conversation
        if ($conversation->user1_id !== auth()->id() && $conversation->user2_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $conversation->update($request->only(['title', 'is_archived']));

        return response()->json($conversation);
    }

    /**
     * Delete a conversation
     */
    public function destroy($id): JsonResponse
    {
        $conversation = Conversation::findOrFail($id);

        // Check if user part of conversation
        if ($conversation->user1_id !== auth()->id() && $conversation->user2_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $conversation->delete();

        return response()->json(['status' => 'Conversation deleted successfully']);
    }

    /**
     * Search for users to start new conversations
     */
    public function searchUsers(Request $request): JsonResponse
    {
        $query = $request->get('q', '');
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }
        
        $users = User::where('id', '!=', auth()->id())
            ->whereNotNull('email_verified_at')
            ->where(function ($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('email', 'LIKE', "%{$query}%");
            })
            ->select('id', 'name', 'email')
            ->orderBy('name')
            ->limit(20)
            ->get();
            
        return response()->json($users);
    }
}
