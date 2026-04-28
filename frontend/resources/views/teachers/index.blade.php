<x-layouts.app :title="'Teachers'">
    <h1>Teachers</h1>

    <div class="card" style="margin-bottom:16px">
        <form method="POST" action="/teachers" class="row">
            @csrf
            <div class="field">
                <label>Name</label>
                <input class="input" name="name" value="{{ old('name') }}" required maxlength="200" />
                @error('name')<div style="color:#dc2626; font-size:12px">{{ $message }}</div>@enderror
            </div>
            <div class="field">
                <label>Email</label>
                <input class="input" name="email" type="email" value="{{ old('email') }}" required maxlength="200" />
                @error('email')<div style="color:#dc2626; font-size:12px">{{ $message }}</div>@enderror
            </div>
            <div class="actions">
                <button class="btn" type="submit">Create</button>
            </div>
        </form>
    </div>

    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Subjects</th>
                    <th style="width:120px"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($teachers as $t)
                    <tr>
                        <td><span class="pill">{{ $t['id'] }}</span></td>
                        <td>{{ $t['name'] ?? '' }}</td>
                        <td>{{ $t['email'] ?? '' }}</td>
                        <td class="muted">{{ isset($t['subjectIds']) ? implode(', ', (array) $t['subjectIds']) : '' }}</td>
                        <td>
                            <form method="POST" action="/teachers/{{ $t['id'] }}" style="margin:0; display:flex; justify-content:flex-end">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-secondary" type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="muted" style="padding:18px 12px">No data</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-layouts.app>

