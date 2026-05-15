<x-layouts.app :title="'Edit subject'">
    <div style="display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap; margin-bottom:12px">
        <h1 style="margin:0">Edit subject</h1>
        <a class="btn btn-secondary" href="/subjects" style="text-decoration:none; display:inline-block">Back</a>
    </div>

    <div class="card">
        <form method="POST" action="/subjects/{{ $subject['id'] ?? '' }}" class="row">
            @csrf
            @method('PUT')
            <div class="field">
                <label>Name</label>
                <input class="input" name="name" value="{{ old('name', $subject['name'] ?? '') }}" required maxlength="200" />
                @error('name')<div style="color:#dc2626; font-size:12px">{{ $message }}</div>@enderror
            </div>
            <div class="field">
                <label>Course</label>
                <select class="input" name="courseId" required>
                    @foreach ($courses as $c)
                        @if (is_array($c) && isset($c['id']))
                            @php($id = (string) $c['id'])
                            <option value="{{ $id }}" @selected(old('courseId', (string) ($subject['courseId'] ?? '')) === $id)>
                                {{ $courseNameById[$id] ?? $id }}
                            </option>
                        @endif
                    @endforeach
                </select>
                @error('courseId')<div style="color:#dc2626; font-size:12px">{{ $message }}</div>@enderror
            </div>
            <div class="actions">
                <button class="btn" type="submit">Save</button>
            </div>
        </form>
    </div>

    <div class="card" style="margin-top:12px">
        <h3>Assign teachers</h3>
        @if (!empty($subject['teacherIds'] ?? []))
            <ul>
                @foreach ($subject['teacherIds'] as $tid)
                    <li>
                        {{ $teacherNameById[$tid] ?? $tid }}
                        <form method="POST" action="/client-api/teachers/{{ $tid }}/unassign" style="display:inline-block; margin-left:8px">
                            @csrf
                            <input type="hidden" name="subjectId" value="{{ $subject['id'] }}" />
                            <button class="btn btn-danger" type="submit">Unassign</button>
                        </form>
                    </li>
                @endforeach
            </ul>
        @endif

        <form method="POST" action="/client-api/teachers/{{ $teachers[0]['id'] ?? '' }}/assign" class="row" id="assign-teacher-form">
            @csrf
            <label>Teacher</label>
            <select name="teacherId" class="input" required>
                @foreach ($teachers as $t)
                    @if (is_array($t) && isset($t['id']))
                        <option value="{{ $t['id'] }}">{{ $t['name'] ?? $t['id'] }}</option>
                    @endif
                @endforeach
            </select>
            <input type="hidden" name="subjectId" value="{{ $subject['id'] }}" />
            <div class="actions">
                <button class="btn" type="submit">Assign</button>
            </div>
        </form>
    </div>
</x-layouts.app>

