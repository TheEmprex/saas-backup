<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\WebPushSubscription;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

class WebPushService
{
    public function __construct(
        private readonly string $publicKey,
        private readonly string $privateKey,
    ) {}

    public static function fromEnv(): self
    {
        return new self(
            publicKey: (string) env('VAPID_PUBLIC_KEY'),
            privateKey: (string) env('VAPID_PRIVATE_KEY'),
        );
    }

    public function sendToUser(int $userId, array $payload): array
    {
        $webPush = new WebPush([
            'VAPID' => [
                'subject' => config('app.url'),
                'publicKey' => $this->publicKey,
                'privateKey' => $this->privateKey,
            ],
        ]);

        $results = [];
        $subs = WebPushSubscription::where('user_id', $userId)->get();
        foreach ($subs as $sub) {
            $subscription = Subscription::create([
                'endpoint' => $sub->endpoint,
                'publicKey' => $sub->p256dh,
                'authToken' => $sub->auth,
                'contentEncoding' => 'aes128gcm',
            ]);
            $results[] = $webPush->sendOneNotification($subscription, json_encode($payload));
        }

        // Flush
        foreach ($webPush->flush() as $report) {
            // Optionally, handle failures
        }

        return $results;
    }
}

