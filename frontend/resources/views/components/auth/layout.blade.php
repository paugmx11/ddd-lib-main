<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Client') }}</title>
        <style>
            body { margin: 0; font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial; background: #fafafa; color: #111827; }
            .container { max-width: 420px; margin: 0 auto; padding: 48px 16px; }
            .card { border: 1px solid #e5e7eb; background: #fff; border-radius: 10px; padding: 16px; }
            .title { font-size: 18px; font-weight: 700; margin: 0; }
            .sub { font-size: 13px; color: #6b7280; margin-top: 4px; }
            .notice { border: 1px solid #e5e7eb; background: #fff; border-radius: 10px; padding: 10px 12px; margin: 16px 0; font-size: 14px; }
            label { display:block; font-size: 12px; font-weight: 600; color: #374151; }
            input { margin-top: 6px; width: 100%; border: 1px solid #d1d5db; border-radius: 8px; padding: 10px 12px; font-size: 14px; box-sizing: border-box; }
            .row { display:flex; gap: 10px; align-items:center; justify-content: space-between; }
            button { border: 1px solid #111827; background: #111827; color: #fff; border-radius: 8px; padding: 10px 12px; cursor: pointer; font-size: 14px; }
            button:hover { background: #0b1220; }
            a { color: #374151; text-decoration: none; }
            a:hover { text-decoration: underline; }
            .err { margin-top: 6px; font-size: 12px; color: #dc2626; }
        </style>
    </head>
    <body>
        <div class="container">
            <div style="margin-bottom:16px">
                <div class="title">School Client</div>
                <div class="sub">Laravel login</div>
            </div>

            @if (session('notice'))
                <div class="notice">{{ session('notice') }}</div>
            @endif

            {{ $slot }}
        </div>
    </body>
</html>
