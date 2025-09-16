<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\MessageSent;
use App\Models\User;
use App\Services\WebPushService;

class SendPushOnMessage
{
    public function handle(MessageSent $event): void
    {
        $message = $event->message;
        $conversation = $message->conversation;

        // Determine the other participant (supports legacy user1/user2)
        $recipientId = null;
        if (!is_null($conversation->user1_id) && !is_null($conversation->user2_id)) {
            $recipientId = $message->sender_id === $conversation->user1_id
                ? $conversation->user2_id
                : $conversation->user1_id;
        } else {
            // Fallback to first participant that's not the sender
            $recipientId = $conversation->participants()
                ->where('user_id', '!=', $message->sender_id)
                ->value('user_id');
        }

        if (!$recipientId) {
            return;
        }

        // Skip if VAPID keys not configured
        if (!env('VAPID_PUBLIC_KEY') || !env('VAPID_PRIVATE_KEY')) {
            return;
        }

        // Compose push payload
        $senderName = $event->sender->name ?? 'New message';
        $body = $message->content ?: ($message->file_name ? ('📎 ' . $message->file_name) : 'New message');
        $payload = [
            'title' => $senderName,
            'body' => $body,
            'icon' => '/pwa-192x192.png',
            'badge' => '/pwa-64x64.png',
            'data' => [
                'url' => url('/messages?c=' . $conversation->id),
                'conversation_id' => $conversation->id,
                'message_id' => $message->id,
            ],
        ];

        // Send
        $service = WebPushService::fromEnv();
        $service->sendToUser((int) $recipientId, $payload);
    }
}

