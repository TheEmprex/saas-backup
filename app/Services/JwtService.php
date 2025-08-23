<?php

namespace App\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Models\User;
use Carbon\Carbon;

class JwtService
{
    private $secretKey;
    private $algorithm = 'HS256';

    public function __construct()
    {
        $this->secretKey = config('app.key') . '_messaging_micro_app';
    }

    /**
     * Generate JWT token for messaging micro-app
     */
    public function generateToken(User $user, int $expirationMinutes = 1440): string
    {
        $payload = [
            'iss' => config('app.url'), // Issuer
            'aud' => 'messaging-app',   // Audience
            'iat' => Carbon::now()->timestamp, // Issued at
            'exp' => Carbon::now()->addMinutes($expirationMinutes)->timestamp, // Expiration
            'user_id' => $user->id,
            'user_email' => $user->email,
            'user_name' => $user->name,
            'user_avatar' => $user->userProfile?->profile_picture,
            'permissions' => [
                'can_send_messages' => true,
                'can_create_conversations' => true,
                'can_upload_files' => true,
            ]
        ];

        return JWT::encode($payload, $this->secretKey, $this->algorithm);
    }

    /**
     * Validate and decode JWT token
     */
    public function validateToken(string $token): ?array
    {
        try {
            $decoded = JWT::decode($token, new Key($this->secretKey, $this->algorithm));
            return (array) $decoded;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get user from JWT token
     */
    public function getUserFromToken(string $token): ?User
    {
        $payload = $this->validateToken($token);
        
        if (!$payload || !isset($payload['user_id'])) {
            return null;
        }

        return User::find($payload['user_id']);
    }
}
