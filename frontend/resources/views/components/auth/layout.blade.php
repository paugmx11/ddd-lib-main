<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Client') }}</title>
        @vite(['resources/css/app.css'])
    </head>
    <body class="min-h-screen bg-zinc-50 text-zinc-900">
        <div class="mx-auto max-w-md px-4 py-12">
            <div class="mb-6">
                <div class="text-xl font-semibold">School Client</div>
                <div class="text-sm text-zinc-500">Laravel login</div>
            </div>

            @if (session('notice'))
                <div class="mb-4 rounded border border-zinc-200 bg-white px-3 py-2 text-sm">
                    {{ session('notice') }}
                </div>
            @endif

            {{ $slot }}
        </div>
    </body>
</html>
