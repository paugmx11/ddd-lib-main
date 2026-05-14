<x-auth.layout>
    <div class="card">
        <h1 style="margin:0 0 14px 0; font-size:14px; font-weight:700">Login</h1>

        <form method="POST" action="/login" style="display:grid; gap:12px">
            @csrf

            <div>
                <label>Email</label>
                <input name="email" value="{{ old('email') }}" />
                @error('email')
                    <div class="err">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label>Password</label>
                <input name="password" type="password" />
                @error('password')
                    <div class="err">{{ $message }}</div>
                @enderror
            </div>

            <div class="row">
                <button type="submit">Login</button>
                <a href="/register">Create account</a>
            </div>
        </form>
        <div style="margin-top:12px">
            @php
                $googleUrl = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query([
                    'client_id' => env('GOOGLE_CLIENT_ID'),
                    'redirect_uri' => env('GOOGLE_REDIRECT_URI'),
                    'response_type' => 'code',
                    'scope' => 'openid email profile',
                    'prompt' => 'select_account',
                ]);
            @endphp
            <a class="button outline" href="{{ $googleUrl }}">Sign in with Google</a>
        </div>
    </div>
</x-auth.layout>
