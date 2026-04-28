<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ $title ?? config('app.name', 'Client') }}</title>
        <style>
            :root { color-scheme: light; }
            body { margin: 0; font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, "Apple Color Emoji", "Segoe UI Emoji"; background: #fafafa; color: #111827; }
            .container { max-width: 960px; margin: 0 auto; padding: 24px 16px; }
            .topbar { border-bottom: 1px solid #e5e7eb; background: #fff; }
            .topbar-inner { max-width: 960px; margin: 0 auto; padding: 12px 16px; display: flex; align-items: center; justify-content: space-between; gap: 12px; }
            .brand { display: flex; align-items: center; gap: 10px; }
            .logo { width: 28px; height: 28px; border-radius: 6px; background: #111827; }
            .brand-title { font-size: 14px; font-weight: 700; line-height: 1.2; }
            .brand-sub { font-size: 12px; color: #6b7280; line-height: 1.2; }
            .nav { display: flex; align-items: center; gap: 6px; flex-wrap: wrap; }
            .nav a { display: inline-block; padding: 6px 10px; border-radius: 6px; text-decoration: none; color: #111827; }
            .nav a:hover { background: #f3f4f6; }
            .btn { border: 1px solid #111827; background: #111827; color: #fff; border-radius: 8px; padding: 8px 12px; cursor: pointer; font-size: 14px; }
            .btn:hover { background: #0b1220; }
            .btn-secondary { border: 1px solid #d1d5db; background: #fff; color: #111827; }
            .btn-secondary:hover { background: #f9fafb; }
            .card { border: 1px solid #e5e7eb; background: #fff; border-radius: 10px; padding: 16px; }
            .notice { border: 1px solid #e5e7eb; background: #fff; border-radius: 10px; padding: 10px 12px; margin-bottom: 12px; font-size: 14px; }
            .error { border-color: #fecaca; background: #fff; }
            h1 { font-size: 18px; margin: 0 0 12px 0; }
            .table-wrap { width: 100%; overflow-x: auto; }
            table { width: 100%; border-collapse: collapse; min-width: 640px; }
            th, td { padding: 10px 12px; border-bottom: 1px solid #e5e7eb; text-align: left; vertical-align: top; font-size: 14px; word-break: break-word; }
            th { font-size: 12px; color: #6b7280; font-weight: 600; }
            .muted { color: #6b7280; }
            .grid { display: grid; gap: 12px; }
            .grid-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
            @media (max-width: 800px) { .grid-3 { grid-template-columns: 1fr; } }
            .field { display: grid; gap: 6px; }
            .field label { font-size: 12px; font-weight: 600; color: #374151; }
            .input { border: 1px solid #d1d5db; border-radius: 8px; padding: 10px 12px; font-size: 14px; box-sizing: border-box; width: 100%; }
            .row { display: flex; gap: 10px; flex-wrap: wrap; align-items: end; }
            .row > * { flex: 1 1 180px; }
            .actions { display: flex; gap: 8px; justify-content: flex-end; flex: 0 0 auto; }
            .pill { display: inline-block; padding: 2px 8px; border-radius: 999px; background: #f3f4f6; color: #111827; font-size: 12px; }
        </style>
    </head>
    <body>
        <div class="topbar">
            <div class="topbar-inner">
                <div class="brand">
                    <div class="logo"></div>
                    <div>
                        <div class="brand-title">School Client</div>
                        <div class="brand-sub">Laravel (server-rendered)</div>
                    </div>
                </div>
                <nav class="nav">
                    <a href="/dashboard">Dashboard</a>
                    <a href="/courses">Courses</a>
                    <a href="/students">Students</a>
                    <a href="/teachers">Teachers</a>
                    <a href="/subjects">Subjects</a>
                    <form method="POST" action="/logout" style="margin:0">
                        @csrf
                        <button class="btn" type="submit">Logout</button>
                    </form>
                </nav>
            </div>
        </div>

        <div class="container">
            @if (session('notice'))
                <div class="notice">{{ session('notice') }}</div>
            @endif
            @if (session('error'))
                <div class="notice error">{{ session('error') }}</div>
            @endif

            {{ $slot }}
        </div>
    </body>
</html>
