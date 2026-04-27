<x-auth.layout>
    <div class="rounded border border-zinc-200 bg-white p-4">
        <h1 class="mb-4 text-sm font-semibold">Login</h1>

        <form method="POST" action="/login" class="space-y-3">
            @csrf

            <div>
                <label class="block text-xs font-medium text-zinc-600">Email</label>
                <input name="email" value="{{ old('email') }}" class="mt-1 w-full rounded border border-zinc-300 px-3 py-2 text-sm" />
                @error('email')
                    <div class="mt-1 text-xs text-red-600">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-medium text-zinc-600">Password</label>
                <input name="password" type="password" class="mt-1 w-full rounded border border-zinc-300 px-3 py-2 text-sm" />
                @error('password')
                    <div class="mt-1 text-xs text-red-600">{{ $message }}</div>
                @enderror
            </div>

            <div class="flex items-center justify-between gap-2">
                <button class="rounded bg-zinc-900 px-3 py-2 text-sm text-white hover:bg-zinc-800" type="submit">Login</button>
                <a class="text-sm text-zinc-600 hover:underline" href="/register">Create account</a>
            </div>
        </form>
    </div>
</x-auth.layout>

