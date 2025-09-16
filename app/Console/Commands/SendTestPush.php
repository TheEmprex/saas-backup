<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\User;
use App\Services\WebPushService;
use Illuminate\Console\Command;

class SendTestPush extends Command
{
    protected $signature = 'push:test {user_id? : Optional user id, defaults to first admin}';
    protected $description = 'Send a test Web Push notification to a user with an active subscription';

    public function handle(): int
    {
        $userId = (int) ($this->argument('user_id') ?? 0);
        if (!$userId) {
            $userId = User::whereHas('roles', fn($q) => $q->where('name', 'admin'))->value('id') ?? User::value('id');
        }
        if (!$userId) {
            $this->error('No users found');
            return self::FAILURE;
        }

        $service = WebPushService::fromEnv();
        $payload = [
            'title' => 'OnlyVerified',
            'body' => 'This is a test notification. 🎉',
            'icon' => '/pwa-192x192.png',
            'badge' => '/pwa-64x64.png',
            'data' => [ 'url' => url('/marketplace') ],
        ];

        $service->sendToUser($userId, $payload);
        $this->info("Test push sent to user ID {$userId} (if they have a subscription)");
        return self::SUCCESS;
    }
}

