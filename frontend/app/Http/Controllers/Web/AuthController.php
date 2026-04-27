<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Services\BackendApi;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

final class AuthController
{
    public function showLogin(): \Illuminate\Contracts\View\View
    {
        return view('auth.login');
    }

    public function showRegister(): \Illuminate\Contracts\View\View
    {
        return view('auth.register');
    }

    public function register(Request $request, BackendApi $backendApi): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:200'],
            'email' => ['required', 'email', 'max:200'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        $user = User::query()->where('email', $data['email'])->first();
        if ($user !== null) {
            throw ValidationException::withMessages([
                'email' => 'This email is already registered.',
            ]);
        }

        $user = User::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        // Mirror the user in the backend and store the backend token in the session.
        $resp = $backendApi->request('POST', '/api/auth/register', [
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
        ], false);

        $payload = $resp->json();
        if (is_array($payload) && isset($payload['token']) && is_string($payload['token']) && $payload['token'] !== '') {
            session(['backend_token' => $payload['token']]);
        } else {
            // Backend registration failed; log out the local user to avoid a broken session.
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            throw ValidationException::withMessages([
                'email' => $payload['error'] ?? 'Backend registration failed.',
            ]);
        }

        return redirect('/')->with('notice', 'Registered successfully.');
    }

    public function login(Request $request, BackendApi $backendApi): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:200'],
            'password' => ['required', 'string', 'min:1'],
        ]);

        if (!Auth::attempt(['email' => $data['email'], 'password' => $data['password']])) {
            throw ValidationException::withMessages([
                'email' => 'Invalid credentials.',
            ]);
        }

        $request->session()->regenerate();

        // Login in backend and store token for API calls.
        $resp = $backendApi->request('POST', '/api/auth/login', [
            'email' => $data['email'],
            'password' => $data['password'],
        ], false);

        if (!$resp->successful()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            $payload = $resp->json();
            throw ValidationException::withMessages([
                'email' => $payload['error'] ?? 'Backend login failed.',
            ]);
        }

        $payload = $resp->json();
        if (!is_array($payload) || !isset($payload['token']) || !is_string($payload['token']) || $payload['token'] === '') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            throw ValidationException::withMessages([
                'email' => $payload['error'] ?? 'Backend login failed (missing token).',
            ]);
        }

        session(['backend_token' => $payload['token']]);

        return redirect('/')->with('notice', 'Logged in.');
    }

    public function logout(Request $request, BackendApi $backendApi): RedirectResponse
    {
        // Best-effort backend logout.
        try {
            $backendApi->request('POST', '/api/auth/logout', [], true);
        } catch (\Throwable) {
            // ignore
        }

        session()->forget('backend_token');

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
