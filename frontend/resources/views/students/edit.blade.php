<x-layouts.app :title="'Edit student'">
    <div style="display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap; margin-bottom:12px">
        <h1 style="margin:0">Edit student</h1>
        <a class="btn btn-secondary" href="/students" style="text-decoration:none; display:inline-block">Back</a>
    </div>

    <div class="card">
        <form method="POST" action="/students/{{ $student['id'] ?? '' }}" class="row">
            @csrf
            @method('PUT')
            <div class="field">
                <label>Name</label>
                <input class="input" name="name" value="{{ old('name', $student['name'] ?? '') }}" required maxlength="200" />
                @error('name')<div style="color:#dc2626; font-size:12px">{{ $message }}</div>@enderror
            </div>
            <div class="field">
                <label>Email</label>
                <input class="input" name="email" type="email" value="{{ old('email', $student['email'] ?? '') }}" required maxlength="200" />
                @error('email')<div style="color:#dc2626; font-size:12px">{{ $message }}</div>@enderror
            </div>
            <div class="actions">
                <button class="btn" type="submit">Save</button>
            </div>
        </form>
    </div>
</x-layouts.app>

