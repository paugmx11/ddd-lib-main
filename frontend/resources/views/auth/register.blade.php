<x-auth.layout>
    <div class="card">
        <h1 style="margin:0 0 14px 0; font-size:14px; font-weight:700">Register</h1>

        <form method="POST" action="/register" style="display:grid; gap:12px">
            @csrf

            <div>
                <label>Name</label>
                <input name="name" value="{{ old('name') }}" />
                @error('name')
                    <div class="err">{{ $message }}</div>
                @enderror
            </div>

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
                <button type="submit">Register</button>
                <a href="/login">I already have an account</a>
            </div>
        </form>
    </div>
</x-auth.layout>
