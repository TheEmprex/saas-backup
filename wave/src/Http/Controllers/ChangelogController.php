<?php

declare(strict_types=1);

namespace Wave\Http\Controllers;

use App\Http\Controllers\Controller;
use Wave\Changelog;

class ChangelogController extends Controller
{
    public function read(): void
    {
        $user = auth()->user();
        Changelog::query()->whereDoesntHave('users', function ($query) use ($user): void {
            $query->where('user_id', $user->id);
        })->get()
            ->pluck('id')
            ->tap(function ($missingChangelogNotifications) use ($user): void {
                $user->changelogs()->attach($missingChangelogNotifications->toArray());
            });
    }
}
