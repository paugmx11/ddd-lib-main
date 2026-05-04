<x-layouts.app :title="'Edit course'">
    <div style="display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap; margin-bottom:12px">
        <h1 style="margin:0">Edit course</h1>
        <a class="btn btn-secondary" href="/courses" style="text-decoration:none; display:inline-block">Back</a>
    </div>

    <div class="card">
        <form method="POST" action="/courses/{{ $course['id'] ?? '' }}" class="row">
            @csrf
            @method('PUT')
            <div class="field">
                <label>Name</label>
                <input class="input" name="name" value="{{ old('name', $course['name'] ?? '') }}" required maxlength="200" />
                @error('name')<div style="color:#dc2626; font-size:12px">{{ $message }}</div>@enderror
            </div>
            <div class="field">
                <label>Start</label>
                <input class="input" name="startDate" type="date" value="{{ old('startDate', $course['startDate'] ?? '') }}" required />
                @error('startDate')<div style="color:#dc2626; font-size:12px">{{ $message }}</div>@enderror
            </div>
            <div class="field">
                <label>End</label>
                <input class="input" name="endDate" type="date" value="{{ old('endDate', $course['endDate'] ?? '') }}" required />
                @error('endDate')<div style="color:#dc2626; font-size:12px">{{ $message }}</div>@enderror
            </div>
            <div class="field" style="flex: 2 1 260px">
                <label>Description (optional)</label>
                <input class="input" name="description" value="{{ old('description', $course['description'] ?? '') }}" maxlength="500" />
                @error('description')<div style="color:#dc2626; font-size:12px">{{ $message }}</div>@enderror
            </div>
            <div class="actions">
                <button class="btn" type="submit">Save</button>
            </div>
        </form>
    </div>
</x-layouts.app>

