<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AppearanceController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function edit()
    {
        $user = auth()->user();
        $prefs = $user->dashboard_preferences ?? [];
        $appearance = $prefs['appearance'] ?? ['theme' => 'system'];

        return view('settings.appearance', [
            'appearance' => $appearance,
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'theme' => 'required|in:light,dark,system',
        ]);

        $user = auth()->user();
        $prefs = $user->dashboard_preferences ?? [];
        $prefs['appearance'] = [
            'theme' => $data['theme'],
        ];
        $user->dashboard_preferences = $prefs;
        $user->save();

        return redirect()->route('settings.appearance.edit')->with('success', 'Appearance updated.');
    }

    public function updateTheme(Request $request)
    {
        $request->merge(['theme' => $request->input('theme')]);
        return $this->update($request);
    }
}

