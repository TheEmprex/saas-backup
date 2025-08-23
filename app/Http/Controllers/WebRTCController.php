<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class WebRTCController extends Controller
{
    /**
     * Handle WebRTC signaling
     */
    public function signal(Request $request)
    {
        $request->validate([
            'signal' => 'required|array',
            'to_user_id' => 'required|exists:users,id'
        ]);

        try {
            $userId = Auth::id();
            $toUserId = $request->get('to_user_id');
            $signal = $request->get('signal');

            // Store the signal in cache for the recipient to pick up
            $cacheKey = 'webrtc_signal_' . $toUserId . '_' . time() . '_' . uniqid();
            
            Cache::put($cacheKey, [
                'from_user_id' => $userId,
                'to_user_id' => $toUserId,
                'signal' => $signal,
                'timestamp' => now()
            ], now()->addMinutes(5)); // Signals expire after 5 minutes

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            Log::error('WebRTC signal error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to send signal'], 500);
        }
    }

    /**
     * Get incoming signals for the current user
     */
    public function getSignals(Request $request)
    {
        try {
            $userId = Auth::id();
            $pattern = 'webrtc_signal_' . $userId . '_*';
            
            // Get all cached signals for this user
            $signals = [];
            $keys = Cache::get($pattern, []);
            
            // In a real implementation, you'd need to implement a better way to get cache keys
            // For now, we'll return a simple structure
            
            return response()->json(['signals' => $signals]);

        } catch (\Exception $e) {
            Log::error('WebRTC get signals error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to get signals'], 500);
        }
    }

    /**
     * Initiate a call
     */
    public function initiateCall(Request $request)
    {
        $request->validate([
            'to_user_id' => 'required|exists:users,id',
            'call_type' => 'required|in:audio,video'
        ]);

        try {
            $userId = Auth::id();
            $toUserId = $request->get('to_user_id');
            $callType = $request->get('call_type');

            // Create a call entry in cache
            $callId = 'call_' . $userId . '_' . $toUserId . '_' . time();
            
            Cache::put($callId, [
                'id' => $callId,
                'caller_id' => $userId,
                'callee_id' => $toUserId,
                'call_type' => $callType,
                'status' => 'ringing',
                'created_at' => now()
            ], now()->addMinutes(10));

            // Add to incoming calls for the recipient
            $incomingKey = 'incoming_calls_' . $toUserId;
            $incomingCalls = Cache::get($incomingKey, []);
            $incomingCalls[] = $callId;
            Cache::put($incomingKey, $incomingCalls, now()->addMinutes(10));

            return response()->json([
                'success' => true,
                'call_id' => $callId
            ]);

        } catch (\Exception $e) {
            Log::error('WebRTC initiate call error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to initiate call'], 500);
        }
    }

    /**
     * Check for incoming calls
     */
    public function checkIncomingCalls(Request $request)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }
        
        try {
            $userId = Auth::id();
            $incomingKey = 'incoming_calls_' . $userId;
            $callIds = Cache::get($incomingKey, []);

            $calls = [];
            foreach ($callIds as $callId) {
                $callData = Cache::get($callId);
                if ($callData && $callData['status'] === 'ringing') {
                    $calls[] = $callData;
                }
            }

            return response()->json(['calls' => $calls]);

        } catch (\Exception $e) {
            Log::error('WebRTC check incoming calls error: ' . $e->getMessage());
            return response()->json(['calls' => []], 200); // Return empty calls array instead of error
        }
    }

    /**
     * Respond to a call (accept/reject)
     */
    public function respondToCall(Request $request)
    {
        $request->validate([
            'call_id' => 'required|string',
            'response' => 'required|in:accept,reject'
        ]);

        try {
            $callId = $request->get('call_id');
            $response = $request->get('response');
            $userId = Auth::id();

            $callData = Cache::get($callId);
            if (!$callData || $callData['callee_id'] !== $userId) {
                return response()->json(['error' => 'Call not found'], 404);
            }

            // Update call status
            $callData['status'] = $response === 'accept' ? 'accepted' : 'rejected';
            $callData['responded_at'] = now();
            Cache::put($callId, $callData, now()->addMinutes(10));

            // Remove from incoming calls
            $incomingKey = 'incoming_calls_' . $userId;
            $incomingCalls = Cache::get($incomingKey, []);
            $incomingCalls = array_filter($incomingCalls, fn($id) => $id !== $callId);
            Cache::put($incomingKey, $incomingCalls, now()->addMinutes(10));

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            Log::error('WebRTC respond to call error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to respond to call'], 500);
        }
    }

    /**
     * End a call
     */
    public function endCall(Request $request)
    {
        $request->validate([
            'call_id' => 'required|string'
        ]);

        try {
            $callId = $request->get('call_id');
            $userId = Auth::id();

            $callData = Cache::get($callId);
            if (!$callData) {
                return response()->json(['error' => 'Call not found'], 404);
            }

            // Verify user is part of the call
            if ($callData['caller_id'] !== $userId && $callData['callee_id'] !== $userId) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            // Update call status
            $callData['status'] = 'ended';
            $callData['ended_at'] = now();
            $callData['ended_by'] = $userId;
            Cache::put($callId, $callData, now()->addMinutes(2)); // Keep for a short time for cleanup

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            Log::error('WebRTC end call error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to end call'], 500);
        }
    }
}

