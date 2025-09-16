<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\BaseController;
use App\Services\LoggingService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class NotificationPreferencesController extends BaseController
{
    public function __construct(LoggingService $logger)
    {
        parent::__construct($logger);
        $this->middleware('auth:api');
    }

    public function show(Request $request): JsonResponse
    {
        $user = $request->user();
        $prefs = $this->getPreferences($user->dashboard_preferences ?? []);

        return $this->successResponse($prefs, 'Notification preferences');
    }

    public function update(Request $request): JsonResponse
    {
        $data = $request->validate([
            'enabled' => 'sometimes|boolean',
            'sounds' => 'sometimes|boolean',
            'quiet_hours_start' => 'nullable|string|max:10',
            'quiet_hours_end' => 'nullable|string|max:10',
            'topics' => 'nullable|array',
            'topics.*' => 'string|max:50',
        ]);

        $user = $request->user();
        $all = $user->dashboard_preferences ?? [];
        $current = $this->getPreferences($all);

        $updated = [
            'enabled' => array_key_exists('enabled', $data) ? (bool) $data['enabled'] : $current['enabled'],
            'sounds' => array_key_exists('sounds', $data) ? (bool) $data['sounds'] : $current['sounds'],
            'quiet_hours' => [
                'start' => $data['quiet_hours_start'] ?? ($current['quiet_hours']['start'] ?? null),
                'end' => $data['quiet_hours_end'] ?? ($current['quiet_hours']['end'] ?? null),
            ],
            'topics' => $data['topics'] ?? ($current['topics'] ?? ['messages']),
        ];

        $all['notifications'] = $updated;
        $user->dashboard_preferences = $all;
        $user->save();

        $this->logActivity('notification_preferences_updated', [
            'topics_count' => count($updated['topics'] ?? []),
            'enabled' => $updated['enabled'] ?? null,
        ]);

        return $this->successResponse($updated, 'Notification preferences updated');
    }

    private function getPreferences(array $prefs): array
    {
        $defaults = [
            'enabled' => true,
            'sounds' => true,
            'quiet_hours' => ['start' => null, 'end' => null],
            'topics' => ['messages'],
        ];

        $existing = $prefs['notifications'] ?? [];
        return array_replace_recursive($defaults, $existing);
    }
}

