<?php

declare(strict_types=1);

namespace App\Http\Controllers\ClientApi;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

final class SessionController
{
    public function status(): JsonResponse
    {
        return response()->json([
            'authenticated' => Auth::check(),
            'backendToken' => (string) session('backend_token', '') !== '',
            'user' => Auth::check() ? [
                'id' => Auth::id(),
                'name' => Auth::user()?->name,
                'email' => Auth::user()?->email,
            ] : null,
        ], 200);
    }
}

