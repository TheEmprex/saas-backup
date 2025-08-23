<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\V1\BaseController;
use App\Http\Requests\Messaging\SendMessageRequest;
use App\Http\Resources\ConversationResource;
use App\Http\Resources\MessageResource;
use App\Services\MessagingService;
use App\DTOs\MessageData;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Psr\Log\LoggerInterface;

class MessagingController extends BaseController
{
    public function __construct(
        private readonly MessagingService $messagingService,
        private readonly LoggerInterface $logger
    ) {
        $this->middleware('auth');
        $this->middleware('throttle:60,1')->only([
            'sendMessage', 
            'updateTyping', 
            'addReaction'
        ]);
    }

    /**
     * Display the messaging interface
     */
    public function index(): JsonResponse
    {
        try {
            $user = Auth::user();
            
            // Update user's online status
            $this->messagingService->updateOnlineStatus(
                $user->id, 
                true,
                null,
                [
                    'user_agent' => request()->userAgent(),
                    'ip_address' => request()->ip(),
                ]
            );
            
            $conversations = $this->messagingService->getConversationsForUser($user->id);
            $onlineUsers = $this->messagingService->getOnlineUsers($user->id);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'conversations' => $conversations,
                    'online_users' => $onlineUsers,
                ]
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Failed to load messaging interface', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to load messaging interface'
            ], 500);
        }
    }

    /**
     * Get conversations for the authenticated user
     */
    public function getConversations(): JsonResponse
    {
        try {
            $conversations = $this->messagingService->getConversationsForUser(Auth::id());
            
            return response()->json([
                'success' => true,
                'data' => ['conversations' => $conversations]
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Failed to get conversations', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to load conversations'
            ], 500);
        }
    }

    /**
     * Get messages for a specific conversation
     */
    public function getMessages(Request $request, int $conversationId): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'page' => 'integer|min:1',
                'per_page' => 'integer|min:1|max:100'
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid pagination parameters',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            $page = $request->get('page', 1);
            $perPage = $request->get('per_page', 50);
            
            $result = $this->messagingService->getMessages($conversationId, Auth::id(), $page, $perPage);
            
            return response()->json([
                'success' => true,
                'data' => $result
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Failed to get messages', [
                'user_id' => Auth::id(),
                'conversation_id' => $conversationId,
                'error' => $e->getMessage()
            ]);
            
            $statusCode = str_contains($e->getMessage(), 'Unauthorized') ? 403 : 500;
            
            return response()->json([
                'success' => false,
                'message' => $statusCode === 403 ? 'Unauthorized access' : 'Failed to load messages'
            ], $statusCode);
        }
    }

    /**
     * Send a new message
     */
    public function sendMessage(SendMessageRequest $request): JsonResponse
    {
        try {
            $messageData = MessageData::fromRequest($request->validated(), Auth::id());
            
            // Handle file upload if present
            if ($request->hasFile('file')) {
                $fileData = $this->messagingService->handleFileUpload(
                    $request->file('file'),
                    $messageData->conversationId ?? 0
                );
                
                $messageData = MessageData::withFile(
                    $messageData->senderId,
                    $messageData->content,
                    $fileData,
                    $messageData->conversationId,
                    $messageData->recipientId,
                    $messageData->replyToId
                );
            }
            
            $message = $this->messagingService->sendMessage($messageData);
            
            return response()->json([
                'success' => true,
                'message' => 'Message sent successfully',
                'data' => new MessageResource($message)
            ], 201);
        } catch (\Exception $e) {
            $this->logger->error('Failed to send message', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'request_data' => $request->except(['file'])
            ]);
            
            $statusCode = str_contains($e->getMessage(), 'Unauthorized') ? 403 : 500;
            
            return response()->json([
                'success' => false,
                'message' => $statusCode === 403 ? 'Unauthorized access' : 'Failed to send message'
            ], $statusCode);
        }
    }

    /**
     * Mark message as read
     */
    public function markAsRead(int $messageId): JsonResponse
    {
        try {
            $this->messagingService->markMessageAsRead($messageId, Auth::id());
            
            return response()->json([
                'success' => true,
                'message' => 'Message marked as read'
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Failed to mark message as read', [
                'user_id' => Auth::id(),
                'message_id' => $messageId,
                'error' => $e->getMessage()
            ]);
            
            $statusCode = str_contains($e->getMessage(), 'Unauthorized') ? 403 : 500;
            
            return response()->json([
                'success' => false,
                'message' => $statusCode === 403 ? 'Unauthorized access' : 'Failed to mark message as read'
            ], $statusCode);
        }
    }

    /**
     * Update typing status
     */
    public function updateTyping(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'conversation_id' => 'required|integer|exists:conversations,id',
                'is_typing' => 'required|boolean',
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid request data',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            $this->messagingService->updateTypingStatus(
                $request->conversation_id,
                Auth::id(),
                $request->is_typing
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Typing status updated'
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Failed to update typing status', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            
            $statusCode = str_contains($e->getMessage(), 'Unauthorized') ? 403 : 500;
            
            return response()->json([
                'success' => false,
                'message' => $statusCode === 403 ? 'Unauthorized access' : 'Failed to update typing status'
            ], $statusCode);
        }
    }

    /**
     * Get typing indicators for a conversation
     */
    public function getTypingIndicators(int $conversationId): JsonResponse
    {
        try {
            $typing = $this->messagingService->getTypingIndicators($conversationId, Auth::id());
            
            return response()->json([
                'success' => true,
                'data' => ['typing' => $typing]
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Failed to get typing indicators', [
                'user_id' => Auth::id(),
                'conversation_id' => $conversationId,
                'error' => $e->getMessage()
            ]);
            
            $statusCode = str_contains($e->getMessage(), 'Unauthorized') ? 403 : 500;
            
            return response()->json([
                'success' => false,
                'message' => $statusCode === 403 ? 'Unauthorized access' : 'Failed to load typing indicators'
            ], $statusCode);
        }
    }

    /**
     * Update user online status
     */
    public function updateOnlineStatus(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'is_online' => 'required|boolean',
                'status_message' => 'nullable|string|max:255',
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid request data',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            $this->messagingService->updateOnlineStatus(
                Auth::id(),
                $request->is_online,
                $request->status_message,
                [
                    'user_agent' => $request->userAgent(),
                    'ip_address' => $request->ip(),
                ]
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Online status updated'
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Failed to update online status', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update online status'
            ], 500);
        }
    }

    /**
     * Get online users
     */
    public function getOnlineUsers(): JsonResponse
    {
        try {
            $onlineUsers = $this->messagingService->getOnlineUsers(Auth::id());
            
            return response()->json([
                'success' => true,
                'data' => ['online_users' => $onlineUsers]
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Failed to get online users', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to load online users'
            ], 500);
        }
    }

    /**
     * Search users for starting new conversations
     */
    public function searchUsers(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'q' => 'required|string|min:2|max:100'
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Search query must be at least 2 characters',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            $users = $this->messagingService->searchUsers($request->q, Auth::id());
            
            return response()->json([
                'success' => true,
                'data' => ['users' => $users]
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Failed to search users', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to search users'
            ], 500);
        }
    }

    /**
     * Add reaction to a message
     */
    public function addReaction(Request $request, int $messageId): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'emoji' => 'required|string|max:10', // Allow for multi-character emojis
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid emoji',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            $reactions = $this->messagingService->addMessageReaction(
                $messageId,
                Auth::id(),
                $request->emoji
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Reaction updated',
                'data' => ['reactions' => $reactions]
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Failed to add reaction', [
                'user_id' => Auth::id(),
                'message_id' => $messageId,
                'error' => $e->getMessage()
            ]);
            
            $statusCode = str_contains($e->getMessage(), 'Unauthorized') ? 403 : 500;
            
            return response()->json([
                'success' => false,
                'message' => $statusCode === 403 ? 'Unauthorized access' : 'Failed to update reaction'
            ], $statusCode);
        }
    }
}
