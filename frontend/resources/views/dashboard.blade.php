<x-layouts.app :title="'Dashboard'">
    <h1>Dashboard</h1>

    <div class="grid grid-3" style="margin-bottom:16px">
        <a class="card" style="text-decoration:none; color:inherit" href="/students">
            <div class="muted" style="font-size:12px">Students</div>
            <div style="font-size:22px; font-weight:800">{{ $counts['students'] ?? '—' }}</div>
        </a>
        <a class="card" style="text-decoration:none; color:inherit" href="/teachers">
            <div class="muted" style="font-size:12px">Teachers</div>
            <div style="font-size:22px; font-weight:800">{{ $counts['teachers'] ?? '—' }}</div>
        </a>
        <a class="card" style="text-decoration:none; color:inherit" href="/subjects">
            <div class="muted" style="font-size:12px">Subjects</div>
            <div style="font-size:22px; font-weight:800">{{ $counts['subjects'] ?? '—' }}</div>
        </a>
    </div>

    <div class="card">
        <div style="display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap">
            <div>
                <div style="font-weight:700">Sessió</div>
                <div class="muted" style="font-size:13px">Usuari autenticat i token del backend.</div>
            </div>
            <div style="display:flex; gap:8px; align-items:center; flex-wrap:wrap; justify-content:flex-end">
                <span class="pill">{{ $userEmail }}</span>
                @if ($hasBackendToken)
                    <span class="pill">Backend token OK</span>
                @else
                    <span class="pill">Missing backend token</span>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>

