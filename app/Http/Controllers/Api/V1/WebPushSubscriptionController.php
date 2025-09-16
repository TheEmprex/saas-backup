<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\WebPushSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class WebPushSubscriptionController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'endpoint' => ['required', 'url'],
            'auth' => ['required', 'string', 'max:255'],
            'p256dh' => ['required', 'string', 'max:255'],
            'ua' => ['nullable', 'string', 'max:255'],
        ]);

        $user = Auth::user();

        $subscription = WebPushSubscription::updateOrCreate(
            ['endpoint' => $data['endpoint']],
            [
                'user_id' => $user->id,
                'auth' => $data['auth'],
                'p256dh' => $data['p256dh'],
                'ua' => $data['ua'] ?? $request->userAgent(),
            ]
        );

        return response()->json(['success' => true, 'subscription_id' => $subscription->id]);
    }

    public function destroy(Request $request)
    {
        $data = $request->validate([
            'endpoint' => ['required', 'url'],
        ]);

        WebPushSubscription::where('endpoint', $data['endpoint'])
            ->where('user_id', Auth::id())
            ->delete();

        return response()->json(['success' => true]);
    }

    public function test(Request $request)
    {
        if (!env('VAPID_PUBLIC_KEY') || !env('VAPID_PRIVATE_KEY')) {
            return response()->json(['success' => false, 'error' => 'VAPID keys not configured'], 422);
        }
        $service = \App\Services\WebPushService::fromEnv();
        $payload = [
            'title' => 'OnlyVerified Test',
            'body' => 'Push notifications are enabled on your device. ✅',
            'icon' => '/pwa-192x192.png',
            'badge' => '/pwa-64x64.png',
            'data' => [ 'url' => url('/marketplace') ],
        ];
        $service->sendToUser(Auth::id(), $payload);
        return response()->json(['success' => true]);
    }
}

