<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AuthenticationService
{
    private const MAX_LOGIN_ATTEMPTS = 5;
    private const LOCKOUT_DURATION = 900; // 15 minutes
    private const SUSPICIOUS_ACTIVITY_THRESHOLD = 10;

    /**
     * Authenticate user with enhanced security
     */
    public function authenticate(string $email, string $password, Request $request): array
    {
        // Check if IP is temporarily blocked
        if ($this->isIpBlocked($request->ip())) {
            Log::warning('Login attempt from blocked IP', [
                'ip' => $request->ip(),
                'email' => $email,
                'user_agent' => $request->userAgent(),
            ]);

            return [
                'success' => false,
                'message' => 'Too many failed attempts. Please try again later.',
                'blocked_until' => $this->getIpBlockExpiry($request->ip()),
            ];
        }

        // Check rate limiting for this email
        if ($this->isEmailRateLimited($email)) {
            $this->incrementFailedAttempts($request->ip(), $email);
            
            return [
                'success' => false,
                'message' => 'Too many login attempts for this account.',
                'retry_after' => $this->getEmailRateLimitExpiry($email),
            ];
        }

        $user = User::where('email', $email)->first();
        
        if (!$user || !Hash::check($password, $user->password)) {
            $this->handleFailedLogin($request, $email);
            
            return [
                'success' => false,
                'message' => 'Invalid credentials.',
            ];
        }

        // Additional security checks
        if ($user->is_banned) {
            Log::warning('Banned user attempted login', [
                'user_id' => $user->id,
                'email' => $email,
                'ip' => $request->ip(),
            ]);

            return [
                'success' => false,
                'message' => 'Account is suspended.',
            ];
        }

        // Check for suspicious login patterns
        if ($this->isSuspiciousLogin($user, $request)) {
            Log::warning('Suspicious login detected', [
                'user_id' => $user->id,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'previous_ip' => $user->last_login_ip,
                'last_login' => $user->last_seen_at,
            ]);

            // Could implement additional verification here (2FA, email verification, etc.)
        }

        // Successful login
        $this->handleSuccessfulLogin($user, $request);
        
        return [
            'success' => true,
            'user' => $user,
            'token' => $user->createToken('auth-token')->plainTextToken,
        ];
    }

    /**
     * Check if two-factor authentication is required
     */
    public function requiresTwoFactor(User $user): bool
    {
        // Implement 2FA logic based on user settings or risk assessment
        return $user->two_factor_enabled ?? false;
    }

    /**
     * Verify two-factor authentication code
     */
    public function verifyTwoFactor(User $user, string $code): bool
    {
        // Implement 2FA verification logic
        // This is a placeholder - you'd integrate with your 2FA provider
        return true;
    }

    /**
     * Logout user with session cleanup
     */
    public function logout(User $user, Request $request): bool
    {
        try {
            // Revoke current access token
            $user->currentAccessToken()?->delete();
            
            // Log successful logout
            Log::info('User logged out', [
                'user_id' => $user->id,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Logout failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Handle password reset with security measures
     */
    public function initiatePasswordReset(string $email, Request $request): bool
    {
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            // Don't reveal if email exists, but log the attempt
            Log::info('Password reset attempted for non-existent email', [
                'email' => $email,
                'ip' => $request->ip(),
            ]);
            return true; // Return true to not reveal email existence
        }

        // Check if too many reset requests
        if ($this->isPasswordResetRateLimited($email)) {
            Log::warning('Password reset rate limited', [
                'email' => $email,
                'ip' => $request->ip(),
            ]);
            return false;
        }

        // Track reset request
        $this->trackPasswordResetRequest($email, $request->ip());
        
        // Generate and send reset token (implement your reset logic here)
        return true;
    }

    /**
     * Track user login activity
     */
    public function trackLoginActivity(User $user, Request $request, bool $success): void
    {
        $activityData = [
            'user_id' => $user->id ?? null,
            'email' => $user->email ?? 'unknown',
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'success' => $success,
            'timestamp' => now()->toISOString(),
            'location' => $this->getLocationFromIp($request->ip()),
        ];

        // Store in cache for recent activity tracking
        $key = "login_activity:user_{$user->id}";
        $activities = Cache::get($key, []);
        array_unshift($activities, $activityData);
        
        // Keep only last 50 activities
        $activities = array_slice($activities, 0, 50);
        Cache::put($key, $activities, now()->addDays(30));

        // Log to main log
        Log::info($success ? 'Successful login' : 'Failed login attempt', $activityData);
    }

    /**
     * Get recent login activity for user
     */
    public function getRecentLoginActivity(int $userId, int $limit = 10): array
    {
        $key = "login_activity:user_{$userId}";
        $activities = Cache::get($key, []);
        
        return array_slice($activities, 0, $limit);
    }

    /**
     * Check for account security issues
     */
    public function checkAccountSecurity(User $user): array
    {
        $issues = [];
        
        // Check password age
        if ($user->password_changed_at && $user->password_changed_at->lt(now()->subMonths(6))) {
            $issues[] = [
                'type' => 'password_age',
                'severity' => 'medium',
                'message' => 'Password is older than 6 months',
            ];
        }

        // Check for suspicious login patterns
        $recentActivity = $this->getRecentLoginActivity($user->id, 20);
        $uniqueIps = count(array_unique(array_column($recentActivity, 'ip')));
        
        if ($uniqueIps > 10) {
            $issues[] = [
                'type' => 'multiple_ips',
                'severity' => 'high',
                'message' => 'Account accessed from many different IP addresses',
            ];
        }

        // Check for concurrent sessions
        $activeSessions = $user->tokens()->count();
        if ($activeSessions > 5) {
            $issues[] = [
                'type' => 'multiple_sessions',
                'severity' => 'medium',
                'message' => 'Multiple active sessions detected',
            ];
        }

        return $issues;
    }

    /**
     * Force logout from all devices
     */
    public function forceLogoutAll(User $user): bool
    {
        try {
            $user->tokens()->delete();
            
            Log::info('User forced logout from all devices', [
                'user_id' => $user->id,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Force logout failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Private helper methods
     */
    private function isIpBlocked(string $ip): bool
    {
        $key = "blocked_ip:{$ip}";
        return Cache::has($key);
    }

    private function getIpBlockExpiry(string $ip): ?Carbon
    {
        $key = "blocked_ip:{$ip}";
        $ttl = Cache::get($key . '_ttl');
        return $ttl ? Carbon::createFromTimestamp($ttl) : null;
    }

    private function isEmailRateLimited(string $email): bool
    {
        $key = "email_rate_limit:" . md5($email);
        $attempts = Cache::get($key, 0);
        return $attempts >= self::MAX_LOGIN_ATTEMPTS;
    }

    private function getEmailRateLimitExpiry(string $email): Carbon
    {
        $key = "email_rate_limit:" . md5($email);
        return now()->addSeconds(self::LOCKOUT_DURATION);
    }

    private function handleFailedLogin(Request $request, string $email): void
    {
        $this->incrementFailedAttempts($request->ip(), $email);
        
        Log::warning('Failed login attempt', [
            'email' => $email,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
    }

    private function incrementFailedAttempts(string $ip, string $email): void
    {
        // Track failed attempts by IP
        $ipKey = "failed_attempts_ip:{$ip}";
        $ipAttempts = Cache::get($ipKey, 0) + 1;
        Cache::put($ipKey, $ipAttempts, now()->addHours(1));

        // Track failed attempts by email
        $emailKey = "email_rate_limit:" . md5($email);
        $emailAttempts = Cache::get($emailKey, 0) + 1;
        Cache::put($emailKey, $emailAttempts, now()->addSeconds(self::LOCKOUT_DURATION));

        // Block IP if too many attempts
        if ($ipAttempts >= self::SUSPICIOUS_ACTIVITY_THRESHOLD) {
            $blockKey = "blocked_ip:{$ip}";
            Cache::put($blockKey, true, now()->addMinutes(30));
            Cache::put($blockKey . '_ttl', now()->addMinutes(30)->timestamp, now()->addMinutes(30));
        }
    }

    private function isSuspiciousLogin(User $user, Request $request): bool
    {
        $currentIp = $request->ip();
        $lastIp = $user->last_login_ip;
        
        // Different country/region (would need GeoIP service)
        // Different device fingerprint
        // Login at unusual hours
        // Multiple rapid login attempts
        
        return $lastIp && $lastIp !== $currentIp && $user->last_seen_at?->gt(now()->subHours(1));
    }

    private function handleSuccessfulLogin(User $user, Request $request): void
    {
        // Update user login information
        $user->update([
            'last_seen_at' => now(),
            'last_login_ip' => $request->ip(),
        ]);

        // Clear failed attempts
        $this->clearFailedAttempts($request->ip(), $user->email);
        
        // Track successful login
        $this->trackLoginActivity($user, $request, true);
    }

    private function clearFailedAttempts(string $ip, string $email): void
    {
        Cache::forget("failed_attempts_ip:{$ip}");
        Cache::forget("email_rate_limit:" . md5($email));
    }

    private function isPasswordResetRateLimited(string $email): bool
    {
        $key = "password_reset:" . md5($email);
        $attempts = Cache::get($key, 0);
        return $attempts >= 3; // Max 3 reset requests per hour
    }

    private function trackPasswordResetRequest(string $email, string $ip): void
    {
        $key = "password_reset:" . md5($email);
        $attempts = Cache::get($key, 0) + 1;
        Cache::put($key, $attempts, now()->addHour());
        
        Log::info('Password reset requested', [
            'email' => $email,
            'ip' => $ip,
        ]);
    }

    private function getLocationFromIp(string $ip): ?string
    {
        // Implement GeoIP lookup if needed
        // This is a placeholder
        return null;
    }
}
