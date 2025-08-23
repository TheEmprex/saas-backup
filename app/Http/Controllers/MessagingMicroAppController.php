<?php

namespace App\Http\Controllers;

use App\Services\JwtService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessagingMicroAppController extends Controller
{
    private $jwtService;

    public function __construct(JwtService $jwtService)
    {
        $this->jwtService = $jwtService;
        $this->middleware('auth');
    }

    /**
     * Launch messaging micro-app with JWT token
     */
    public function launch(Request $request)
    {
        $user = Auth::user();
        
        // Generate JWT token for the messaging app
        $token = $this->jwtService->generateToken($user);
        
        // Get messaging app URL from config
        $messagingAppUrl = config('app.messaging_app_url', 'http://localhost:3000');
        
        // If it's an AJAX request, return JSON
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'token' => $token,
                'messaging_app_url' => $messagingAppUrl
            ]);
        }
        
        // Render the messaging app view within Laravel layout
        return view('messaging.app', [
            'token' => $token,
            'messagingAppUrl' => $messagingAppUrl,
            'user' => $user
        ]);
    }

    /**
     * Validate JWT token for micro-app (API endpoint)
     */
    public function validateToken(Request $request)
    {
        $token = $request->input('token') ?? $request->bearerToken();
        
        if (!$token) {
            return response()->json([
                'success' => false,
                'error' => 'Token required'
            ], 401);
        }

        $payload = $this->jwtService->validateToken($token);
        
        if (!$payload) {
            return response()->json([
                'success' => false,
                'error' => 'Invalid or expired token'
            ], 401);
        }

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $payload['user_id'],
                'name' => $payload['user_name'],
                'email' => $payload['user_email'],
                'avatar' => $payload['user_avatar'] ?? null,
            ],
            'permissions' => $payload['permissions']
        ]);
    }

    /**
     * Generate new token (refresh)
     */
    public function refreshToken(Request $request)
    {
        $user = Auth::user();
        $newToken = $this->jwtService->generateToken($user);
        
        return response()->json([
            'success' => true,
            'token' => $newToken
        ]);
    }
}
