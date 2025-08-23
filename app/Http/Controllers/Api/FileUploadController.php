<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\ConversationParticipant;

class FileUploadController extends Controller
{
    /**
     * Handle file upload for messages
     */
    public function uploadMessageFiles(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'files' => 'required|array|max:3',
            'files.*' => 'required|file|max:5120', // 5MB max per file
            'conversation_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Authorization: ensure current user is part of the conversation
        $conversationId = (int) $request->input('conversation_id');
        if (!$this->userCanAccessConversation($conversationId)) {
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }

        try {
            $uploadedFiles = [];

            foreach ($request->file('files') as $file) {
                // Validate file type
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp',
                               'video/mp4', 'video/avi', 'video/mov',
                               'audio/mp3', 'audio/wav', 'audio/ogg',
                               'application/pdf', 'application/msword',
                               'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];

                if (!in_array($file->getMimeType(), $allowedTypes)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'File type not allowed: ' . $file->getClientOriginalName()
                    ], 422);
                }

                // Generate unique filename
                $extension = $file->getClientOriginalExtension();
                $filename = Str::uuid() . '.' . $extension;
                
                // Store file
                $path = $file->storeAs('messages/' . $conversationId, $filename, 'public');
                
                $uploadedFiles[] = [
                    'original_name' => $file->getClientOriginalName(),
                    'filename' => $filename,
                    'path' => $path,
                    'url' => Storage::url($path),
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                ];
            }

            // Here you would typically save the file information to your database
            // For now, we'll just return the uploaded files info

            return response()->json([
                'success' => true,
                'files' => $uploadedFiles,
                'message' => 'Files uploaded successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Upload failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get uploaded file
     */
    public function getFile($conversationId, $filename)
    {
        // Authorization: ensure current user is part of the conversation
        $conversationId = (int) $conversationId;
        if (!$this->userCanAccessConversation($conversationId)) {
            abort(403, 'Forbidden');
        }

        $safeFilename = basename($filename);
        $path = "messages/{$conversationId}/{$safeFilename}";
        
        if (!Storage::disk('public')->exists($path)) {
            abort(404, 'File not found');
        }

        return Storage::disk('public')->response($path);
    }

    /**
     * Delete uploaded file
     */
    public function deleteFile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'path' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Expect path like messages/{conversationId}/{filename}
        $path = $request->string('path');
        $matches = [];
        if (!preg_match('#^messages\/(\d+)\/[^\/]+$#', $path, $matches)) {
            return response()->json(['success' => false, 'message' => 'Invalid path'], 422);
        }
        $conversationId = (int) ($matches[1] ?? 0);
        if (!$this->userCanAccessConversation($conversationId)) {
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }

        try {
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
                
                return response()->json([
                    'success' => true,
                    'message' => 'File deleted successfully'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'File not found'
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Delete failed: ' . $e->getMessage()
            ], 500);
        }
    }

    private function userCanAccessConversation(int $conversationId): bool
    {
        $userId = Auth::id();
        if (!$userId || $conversationId <= 0) {
            return false;
        }
        return ConversationParticipant::where('conversation_id', $conversationId)
            ->where('user_id', $userId)
            ->exists();
    }
}
