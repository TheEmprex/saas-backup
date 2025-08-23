<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Conversation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class MessageController extends Controller
{
    /**
     * Get all messages for a conversation
     */
    public function index($conversationId): JsonResponse
    {
        $conversation = Conversation::findOrFail($conversationId);
        
        // Ensure user has access to this conversation
        $userId = auth()->id();
        if ($conversation->user1_id !== $userId && $conversation->user2_id !== $userId) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $messages = $conversation->messages()
            ->with(['sender:id,name,email', 'replyTo:id,content,sender_id'])
            ->orderBy('created_at')
            ->get();

        return response()->json($messages);
    }

    /**
     * Store a newly sent message
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'conversation_id' => 'required|exists:conversations,id',
            'content' => 'nullable|string',
            'message_type' => 'required|string|in:text,image,video,audio,file,call',
            'file_url' => 'nullable|string',
            'file_name' => 'nullable|string',
            'file_size' => 'nullable|integer',
            'reply_to_id' => 'nullable|exists:messages,id'
        ]);

        $data['sender_id'] = auth()->id();
        
        // Validate user has access to conversation
        $conversation = Conversation::findOrFail($data['conversation_id']);
        $userId = auth()->id();
        if ($conversation->user1_id !== $userId && $conversation->user2_id !== $userId) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $message = Message::create($data);
        $message->load('sender:id,name,email');

        // Update conversation's last message
        $conversation->update([
            'last_message_id' => $message->id,
            'updated_at' => now()
        ]);

        return response()->json($message, 201);
    }

    /**
     * Update the specified message (for editing)
     */
    public function update(Request $request, $id): JsonResponse
    {
        $message = Message::findOrFail($id);
        
        // Only sender can edit message
        if ($message->sender_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $data = $request->validate([
            'content' => 'required|string'
        ]);

        $data['edited_at'] = now();
        $message->update($data);
        $message->load('sender:id,name,email');

        return response()->json($message);
    }

    /**
     * Delete a specific message
     */
    public function destroy($id): JsonResponse
    {
        $message = Message::findOrFail($id);
        
        // Only sender can delete message
        if ($message->sender_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $message->delete();

        return response()->json(['status' => 'Message deleted successfully']);
    }

    /**
     * Mark message as read
     */
    public function markAsRead($id): JsonResponse
    {
        $message = Message::findOrFail($id);
        $message->markAsReadBy(auth()->id());
        
        return response()->json(['status' => 'Message marked as read']);
    }

    /**
     * Add reaction to message
     */
    public function addReaction(Request $request, $id): JsonResponse
    {
        $message = Message::findOrFail($id);
        
        $data = $request->validate([
            'emoji' => 'required|string|max:10'
        ]);
        
        $message->addReaction($data['emoji'], auth()->id());
        $message->load('sender:id,name,email');
        
        return response()->json($message);
    }

    /**
     * Upload file for message
     */
    public function uploadFile(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|max:10240', // 10MB max
        ]);

        $file = $request->file('file');
        $path = $file->store('messages', 'public');
        
        return response()->json([
            'file_url' => Storage::url($path),
            'file_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'message_type' => $this->getFileType($file->getMimeType())
        ]);
    }

    /**
     * Get file type based on mime type
     */
    private function getFileType($mimeType): string
    {
        if (str_starts_with($mimeType, 'image/')) {
            return 'image';
        } elseif (str_starts_with($mimeType, 'video/')) {
            return 'video';
        } elseif (str_starts_with($mimeType, 'audio/')) {
            return 'audio';
        } else {
            return 'file';
        }
    }
}
