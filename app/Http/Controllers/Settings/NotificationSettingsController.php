<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationSettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function edit()
    {
        $user = auth()->user();
        $prefs = $this->getPreferences($user->dashboard_preferences ?? []);

        return view('settings.notifications', [
            'prefs' => $prefs,
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'enabled' => 'sometimes|boolean',
            'sounds' => 'sometimes|boolean',
            'quiet_hours_start' => 'nullable|string|max:10',
            'quiet_hours_end' => 'nullable|string|max:10',
            'topics' => 'nullable|array',
            'topics.*' => 'string|max:50',
        ]);

        $user = auth()->user();
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

        return redirect()->route('settings.notifications.edit')->with('success', 'Notification preferences updated.');
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

