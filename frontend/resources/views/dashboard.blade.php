<x-layouts.app :title="'Dashboard'">
    <h1>Dashboard</h1>

    <div class="grid grid-4" style="margin-bottom:16px">
        <a class="card" style="text-decoration:none; color:inherit" href="/courses">
            <div class="muted" style="font-size:12px">Courses</div>
            <div style="font-size:22px; font-weight:800">{{ $counts['courses'] ?? '—' }}</div>
        </a>
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
</x-layouts.app>
