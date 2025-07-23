<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class WebRTCController extends Controller
{
    /**
     * Handle WebRTC signaling for video/audio calls
     */
    public function signal(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:offer,answer,ice-candidate',
            'data' => 'required|array',
            'target_user_id' => 'required|exists:users,id',
            'call_id' => 'required|string'
        ]);

        $userId = Auth::id();
        $targetUserId = $validated['target_user_id'];
        $callId = $validated['call_id'];
        
        // Store signaling data in cache for real-time exchange
        $signalKey = "webrtc_signal_{$callId}_{$targetUserId}";
        $signalData = [
            'type' => $validated['type'],
            'data' => $validated['data'],
            'from_user_id' => $userId,
            'timestamp' => now()->toISOString()
        ];
        
        Cache::put($signalKey, $signalData, 300); // 5 minutes
        
        return response()->json(['success' => true]);
    }
    
    /**
     * Get pending signals for a user
     */
    public function getSignals(Request $request)
    {
        $userId = Auth::id();
        $callId = $request->get('call_id');
        
        if (!$callId) {
            return response()->json(['signals' => []]);
        }
        
        $signalKey = "webrtc_signal_{$callId}_{$userId}";
        $signal = Cache::get($signalKey);
        
        if ($signal) {
            Cache::forget($signalKey); // Remove after retrieving
            return response()->json(['signals' => [$signal]]);
        }
        
        return response()->json(['signals' => []]);
    }
    
    /**
     * Initiate a call
     */
    public function initiateCall(Request $request)
    {
        $validated = $request->validate([
            'target_user_id' => 'required|exists:users,id',
            'call_type' => 'required|in:video,audio'
        ]);
        
        $callId = uniqid('call_', true);
        $userId = Auth::id();
        
        // Store call information
        $callData = [
            'call_id' => $callId,
            'initiator_id' => $userId,
            'target_user_id' => $validated['target_user_id'],
            'call_type' => $validated['call_type'],
            'status' => 'initiated',
            'created_at' => now()->toISOString()
        ];
        
        Cache::put("call_{$callId}", $callData, 3600); // 1 hour
        
        // Notify target user about incoming call
        $notificationKey = "incoming_call_{$validated['target_user_id']}";
        Cache::put($notificationKey, $callData, 300); // 5 minutes
        
        return response()->json([
            'success' => true,
            'call_id' => $callId,
            'call_data' => $callData
        ]);
    }
    
    /**
     * Check for incoming calls
     */
    public function checkIncomingCalls()
    {
        $userId = Auth::id();
        $notificationKey = "incoming_call_{$userId}";
        $callData = Cache::get($notificationKey);
        
        if ($callData) {
            return response()->json([
                'has_incoming_call' => true,
                'call_data' => $callData
            ]);
        }
        
        return response()->json(['has_incoming_call' => false]);
    }
    
    /**
     * Accept or reject a call
     */
    public function respondToCall(Request $request)
    {
        $validated = $request->validate([
            'call_id' => 'required|string',
            'response' => 'required|in:accept,reject'
        ]);
        
        $userId = Auth::id();
        $callId = $validated['call_id'];
        $response = $validated['response'];
        
        // Update call status
        $callKey = "call_{$callId}";
        $callData = Cache::get($callKey);
        
        if ($callData && $callData['target_user_id'] == $userId) {
            $callData['status'] = $response === 'accept' ? 'accepted' : 'rejected';
            $callData['response_at'] = now()->toISOString();
            
            Cache::put($callKey, $callData, 3600);
            
            // Clear incoming call notification
            Cache::forget("incoming_call_{$userId}");
            
            return response()->json([
                'success' => true,
                'call_data' => $callData
            ]);
        }
        
        return response()->json(['success' => false, 'error' => 'Call not found'], 404);
    }
    
    /**
     * End a call
     */
    public function endCall(Request $request)
    {
        $validated = $request->validate([
            'call_id' => 'required|string'
        ]);
        
        $callId = $validated['call_id'];
        $userId = Auth::id();
        
        // Update call status
        $callKey = "call_{$callId}";
        $callData = Cache::get($callKey);
        
        if ($callData && ($callData['initiator_id'] == $userId || $callData['target_user_id'] == $userId)) {
            $callData['status'] = 'ended';
            $callData['ended_at'] = now()->toISOString();
            $callData['ended_by'] = $userId;
            
            Cache::put($callKey, $callData, 3600);
            
            // Clean up any pending signals
            $signalKeys = [
                "webrtc_signal_{$callId}_{$callData['initiator_id']}",
                "webrtc_signal_{$callId}_{$callData['target_user_id']}"
            ];
            
            foreach ($signalKeys as $key) {
                Cache::forget($key);
            }
            
            return response()->json([
                'success' => true,
                'call_data' => $callData
            ]);
        }
        
        return response()->json(['success' => false, 'error' => 'Call not found'], 404);
    }
}
