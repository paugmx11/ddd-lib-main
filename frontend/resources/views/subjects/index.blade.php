<x-layouts.app :title="'Subjects'">
    <h1>Subjects</h1>

    <div class="card" style="margin-bottom:16px">
        <form method="POST" action="/subjects" class="row">
            @csrf
            <div class="field">
                <label>Name</label>
                <input class="input" name="name" value="{{ old('name') }}" required maxlength="200" />
                @error('name')<div style="color:#dc2626; font-size:12px">{{ $message }}</div>@enderror
            </div>
            <div class="field">
                <label>Course ID</label>
                <input class="input" name="courseId" value="{{ old('courseId') }}" required maxlength="200" />
                @error('courseId')<div style="color:#dc2626; font-size:12px">{{ $message }}</div>@enderror
            </div>
            <div class="actions">
                <button class="btn" type="submit">Create</button>
            </div>
        </form>
        <div class="muted" style="font-size:12px; margin-top:10px">
            Nota: per crear una assignatura necessites un `courseId` existent.
        </div>
    </div>

    <div class="card">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Course</th>
                        <th>Teachers</th>
                        <th style="width:120px"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($subjects as $s)
                        <tr>
                            <td style="font-weight:600">{{ $s['name'] ?? '' }}</td>
                            <td class="muted">{{ $s['courseId'] ?? '' }}</td>
                            <td class="muted">{{ isset($s['teacherIds']) ? implode(', ', (array) $s['teacherIds']) : '' }}</td>
                            <td>
                                <form method="POST" action="/subjects/{{ $s['id'] }}" style="margin:0; display:flex; justify-content:flex-end">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-secondary" type="submit">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="muted" style="padding:18px 12px">No data</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.app>
