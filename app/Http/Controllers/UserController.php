<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    /**
     * Get the online status of a user
     */
    public function status(User $user): JsonResponse
    {
        $isOnline = $user->isOnline();
        return response()->json(['online' => $isOnline]);
    }
}
