<x-layouts.app :title="'Courses'">
    <h1>Courses</h1>

    <div class="card" style="margin-bottom:16px">
        <form method="POST" action="/courses" class="row">
            @csrf
            <div class="field">
                <label>Name</label>
                <input class="input" name="name" value="{{ old('name') }}" required maxlength="200" />
                @error('name')<div style="color:#dc2626; font-size:12px">{{ $message }}</div>@enderror
            </div>
            <div class="field">
                <label>Start</label>
                <input class="input" name="startDate" type="date" value="{{ old('startDate') }}" required />
                @error('startDate')<div style="color:#dc2626; font-size:12px">{{ $message }}</div>@enderror
            </div>
            <div class="field">
                <label>End</label>
                <input class="input" name="endDate" type="date" value="{{ old('endDate') }}" required />
                @error('endDate')<div style="color:#dc2626; font-size:12px">{{ $message }}</div>@enderror
            </div>
            <div class="field" style="flex: 2 1 260px">
                <label>Description (optional)</label>
                <input class="input" name="description" value="{{ old('description') }}" maxlength="500" />
                @error('description')<div style="color:#dc2626; font-size:12px">{{ $message }}</div>@enderror
            </div>
            <div class="actions">
                <button class="btn" type="submit">Create</button>
            </div>
        </form>
    </div>

    <div class="card">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Dates</th>
                        <th>Description</th>
                        <th style="width:120px"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($courses as $c)
                        <tr>
                            <td style="font-weight:600">{{ $c['name'] ?? '' }}</td>
                            <td class="muted">{{ ($c['startDate'] ?? '') . ' → ' . ($c['endDate'] ?? '') }}</td>
                            <td class="muted">{{ $c['description'] ?? '' }}</td>
                            <td>
                                <form method="POST" action="/courses/{{ $c['id'] }}" style="margin:0; display:flex; justify-content:flex-end">
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

