<x-layouts.app :title="'Students'">
    <h1>Students</h1>

    <div class="card" style="margin-bottom:16px">
        <form method="POST" action="/students" class="row">
            @csrf
            <div class="field">
                <label>Name</label>
                <input class="input" name="name" value="{{ old('name') }}" required maxlength="200" />
                @error('name')<div class="muted" style="color:#dc2626; font-size:12px">{{ $message }}</div>@enderror
            </div>
            <div class="field">
                <label>Email</label>
                <input class="input" name="email" type="email" value="{{ old('email') }}" required maxlength="200" />
                @error('email')<div class="muted" style="color:#dc2626; font-size:12px">{{ $message }}</div>@enderror
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
                        <th>Email</th>
                        <th style="width:200px"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($students as $s)
                        <tr>
                            <td style="font-weight:600">{{ $s['name'] ?? '' }}</td>
                            <td>{{ $s['email'] ?? '' }}</td>
                            <td>
                                <div style="display:flex; gap:8px; justify-content:flex-end">
                                    <a class="btn btn-secondary" href="/students/{{ $s['id'] }}/edit" style="text-decoration:none; display:inline-block">Edit</a>
                                    <form method="POST" action="/students/{{ $s['id'] }}" style="margin:0">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-secondary" type="submit">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="muted" style="padding:18px 12px">No data</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.app>
