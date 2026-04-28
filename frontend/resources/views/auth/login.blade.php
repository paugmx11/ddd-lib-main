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
    </div>
</x-auth.layout>
