<x-layouts.app :title="'Edit teacher'">
    <div style="display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap; margin-bottom:12px">
        <h1 style="margin:0">Edit teacher</h1>
        <a class="btn btn-secondary" href="/teachers" style="text-decoration:none; display:inline-block">Back</a>
    </div>

    <div class="card">
        <form method="POST" action="/teachers/{{ $teacher['id'] ?? '' }}" class="row">
            @csrf
            @method('PUT')
            <div class="field">
                <label>Name</label>
                <input class="input" name="name" value="{{ old('name', $teacher['name'] ?? '') }}" required maxlength="200" />
                @error('name')<div style="color:#dc2626; font-size:12px">{{ $message }}</div>@enderror
            </div>
            <div class="field">
                <label>Email</label>
                <input class="input" name="email" type="email" value="{{ old('email', $teacher['email'] ?? '') }}" required maxlength="200" />
                @error('email')<div style="color:#dc2626; font-size:12px">{{ $message }}</div>@enderror
            </div>
            <div class="actions">
                <button class="btn" type="submit">Save</button>
            </div>
        </form>
    </div>

    <div class="card" style="margin-top:12px">
        <h3>Assign subjects</h3>
        @if (!empty($teacher['subjects'] ?? []))
            <ul>
                @foreach ($teacher['subjects'] as $s)
                    <li>
                        {{ $s['name'] ?? $s['id'] }}
                        <form method="POST" action="/client-api/teachers/{{ $teacher['id'] }}/unassign" style="display:inline-block; margin-left:8px">
                            @csrf
                            <input type="hidden" name="subjectId" value="{{ $s['id'] }}" />
                            <button class="btn btn-danger" type="submit">Unassign</button>
                        </form>
                    </li>
                @endforeach
            </ul>
        @endif

        <form method="POST" action="/client-api/teachers/{{ $teacher['id'] }}/assign" class="row">
            @csrf
            <label>Subject</label>
            <select name="subjectId" class="input" required>
                @foreach ($subjectNameById as $sid => $sname)
                    <option value="{{ $sid }}">{{ $sname }}</option>
                @endforeach
            </select>
            <div class="actions">
                <button class="btn" type="submit">Assign</button>
            </div>
        </form>
    </div>
</x-layouts.app>

