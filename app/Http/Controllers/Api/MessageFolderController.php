<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MessageFolder;
use App\Models\Message;
use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class MessageFolderController extends Controller
{
    public function apiIndex(): JsonResponse
    {
        $folders = MessageFolder::where('user_id', Auth::id())
            ->orderBy('order')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $folders,
        ]);
    }

    public function apiStore(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'color' => 'nullable|string|max:7',
            'icon' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $folder = MessageFolder::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'color' => $request->color ?? '#3B82F6',
            'icon' => $request->icon ?? 'folder',
            'order' => MessageFolder::where('user_id', Auth::id())->count(),
        ]);

        return response()->json([
            'success' => true,
            'data' => $folder,
        ], 201);
    }

    public function apiShow(MessageFolder $folder): JsonResponse
    {
        if ($folder->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $folder,
        ]);
    }

    public function apiUpdate(Request $request, MessageFolder $folder): JsonResponse
    {
        if ($folder->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'color' => 'sometimes|nullable|string|max:7',
            'icon' => 'sometimes|nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $folder->update($request->only(['name', 'color', 'icon']));

        return response()->json([
            'success' => true,
            'data' => $folder,
        ]);
    }

    public function update(Request $request, MessageFolder $folder): JsonResponse
    {
        return $this->apiUpdate($request, $folder);
    }

    public function apiDestroy(MessageFolder $folder): JsonResponse
    {
        if ($folder->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        // Move messages back to default folder (null folder_id)
        Message::where('folder_id', $folder->id)->update(['folder_id' => null]);

        $folder->delete();

        return response()->json([
            'success' => true,
            'message' => 'Folder deleted successfully',
        ]);
    }

    public function messages(MessageFolder $folder): JsonResponse
    {
        if ($folder->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $messages = Message::where('folder_id', $folder->id)
            ->with(['sender', 'recipient'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $messages,
        ]);
    }

    public function moveMessages(Request $request, MessageFolder $folder): JsonResponse
    {
        if ($folder->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'message_ids' => 'required|array',
            'message_ids.*' => 'required|integer|exists:messages,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $userId = Auth::id();
        $messageIds = $request->message_ids;

        // Only move messages that belong to the user (either as sender or recipient)
        $updated = Message::whereIn('id', $messageIds)
            ->where(function ($query) use ($userId) {
                $query->where('sender_id', $userId)
                      ->orWhere('recipient_id', $userId);
            })
            ->update(['folder_id' => $folder->id]);

        return response()->json([
            'success' => true,
            'message' => "Moved {$updated} message(s) to folder",
        ]);
    }

    public function reorder(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'folders' => 'required|array',
            'folders.*.id' => 'required|integer|exists:message_folders,id',
            'folders.*.order' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $userId = Auth::id();

        foreach ($request->folders as $folderData) {
            MessageFolder::where('id', $folderData['id'])
                ->where('user_id', $userId)
                ->update(['order' => $folderData['order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Folder order updated successfully',
        ]);
    }

    /**
     * List conversation IDs assigned to this folder
     */
    public function conversations(MessageFolder $folder): JsonResponse
    {
        if ($folder->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $ids = DB::table('conversation_folder')
            ->where('message_folder_id', $folder->id)
            ->pluck('conversation_id')
            ->values();

        return response()->json([
            'success' => true,
            'data' => [
                'conversation_ids' => $ids,
            ],
        ]);
    }

    /**
     * Add a conversation to a folder
     */
    public function addConversation(Request $request, MessageFolder $folder): JsonResponse
    {
        if ($folder->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'conversation_id' => 'required|integer|exists:conversations,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $conversation = Conversation::findOrFail((int) $request->conversation_id);
        $userId = Auth::id();
        if (!$conversation->hasParticipant($userId)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized conversation',
            ], 403);
        }

        DB::table('conversation_folder')->updateOrInsert([
            'message_folder_id' => $folder->id,
            'conversation_id' => $conversation->id,
        ], [
            'updated_at' => now(),
            'created_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Conversation added to folder',
        ]);
    }

    /**
     * Remove a conversation from a folder
     */
    public function removeConversation(MessageFolder $folder, int $conversationId): JsonResponse
    {
        if ($folder->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        DB::table('conversation_folder')
            ->where('message_folder_id', $folder->id)
            ->where('conversation_id', $conversationId)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Conversation removed from folder',
        ]);
    }
}
