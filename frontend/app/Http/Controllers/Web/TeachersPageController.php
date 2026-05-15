<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Services\BackendApi;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class TeachersPageController
{
    public function index(BackendApi $backendApi): View
    {
        $teachers = [];
        $subjectNameById = [];
        $error = '';

        try {
            $resp = $backendApi->request('GET', '/api/teachers');
            if ($resp->successful() && is_array($resp->json())) {
                $teachers = $resp->json();
            } else {
                $payload = $resp->json();
                $error = is_array($payload) ? (string) ($payload['error'] ?? 'Backend error') : 'Backend error';
            }

            $sr = $backendApi->request('GET', '/api/subjects');
            if ($sr->successful() && is_array($sr->json())) {
                foreach ($sr->json() as $s) {
                    if (is_array($s) && isset($s['id'], $s['name'])) {
                        $subjectNameById[(string) $s['id']] = (string) $s['name'];
                    }
                }
            }
        } catch (\Throwable) {
            $error = 'Cannot reach backend';
        }

        if ($error !== '') {
            session()->flash('error', $error);
        }

        return view('teachers.index', [
            'teachers' => $teachers,
            'subjectNameById' => $subjectNameById,
        ]);
    }

    public function store(Request $request, BackendApi $backendApi): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:200'],
            'email' => ['required', 'email', 'max:200'],
        ]);

        $resp = $backendApi->request('POST', '/api/teachers', $data);
        if (!$resp->successful()) {
            $payload = $resp->json();
            return back()->withInput()->with('error', is_array($payload) ? ($payload['error'] ?? 'Backend error') : 'Backend error');
        }

        return redirect('/teachers')->with('notice', 'Teacher created.');
    }

    public function edit(string $id, BackendApi $backendApi): View|RedirectResponse
    {
        try {
            $resp = $backendApi->request('GET', "/api/teachers/{$id}");
            $sr = $backendApi->request('GET', '/api/subjects');
        } catch (\Throwable) {
            return redirect('/teachers')->with('error', 'Cannot reach backend');
        }

        if (!$resp->successful()) {
            $payload = $resp->json();
            $message = is_array($payload) ? (string) ($payload['error'] ?? 'Backend error') : 'Backend error';
            return redirect('/teachers')->with('error', $message);
        }

        $teacher = $resp->json();
        if (!is_array($teacher)) {
            return redirect('/teachers')->with('error', 'Backend error');
        }

        $subjectNameById = [];
        if (isset($sr) && $sr->successful() && is_array($sr->json())) {
            foreach ($sr->json() as $s) {
                if (is_array($s) && isset($s['id'], $s['name'])) {
                    $subjectNameById[(string) $s['id']] = (string) $s['name'];
                }
            }
        }

        // Normalize teacher payload so the view can render full subject objects
        // The backend currently returns subjectIds; convert them to ['id' => ..., 'name' => ...]
        $teacherSubjects = [];
        if (is_array($teacher) && isset($teacher['subjectIds']) && is_array($teacher['subjectIds'])) {
            foreach ($teacher['subjectIds'] as $sid) {
                $sidStr = (string) $sid;
                $teacherSubjects[] = [
                    'id' => $sidStr,
                    'name' => $subjectNameById[$sidStr] ?? $sidStr,
                ];
            }
        }
        // Ensure the key exists for the view (possibly empty array)
        if (is_array($teacher)) {
            $teacher['subjects'] = $teacherSubjects;
        }

        return view('teachers.edit', [
            'teacher' => $teacher,
            'subjectNameById' => $subjectNameById,
        ]);
    }

    public function update(string $id, Request $request, BackendApi $backendApi): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:200'],
            'email' => ['required', 'email', 'max:200'],
        ]);

        $resp = $backendApi->request('PUT', "/api/teachers/{$id}", $data);
        if (!$resp->successful()) {
            $payload = $resp->json();
            return back()->withInput()->with('error', is_array($payload) ? ($payload['error'] ?? 'Backend error') : 'Backend error');
        }

        return redirect('/teachers')->with('notice', 'Teacher updated.');
    }

    public function destroy(string $id, BackendApi $backendApi): RedirectResponse
    {
        $resp = $backendApi->request('DELETE', "/api/teachers/{$id}");
        if (!$resp->successful() && $resp->status() !== 204) {
            $payload = $resp->json();
            return back()->with('error', is_array($payload) ? ($payload['error'] ?? 'Backend error') : 'Backend error');
        }

        return redirect('/teachers')->with('notice', 'Teacher deleted.');
    }

    public function assign(string $id, Request $request, BackendApi $backendApi): RedirectResponse
    {
        $data = $request->validate([
            'subjectId' => ['required', 'string', 'max:200'],
        ]);

        $resp = $backendApi->request('POST', "/api/teachers/{$id}/assign", $data);
        if ($resp->successful()) {
            return back()->with('notice', 'Subject assigned to teacher.');
        }

        $payload = $resp->json();
        $message = is_array($payload) ? ($payload['error'] ?? 'Backend error') : 'Backend error';
        return back()->with('error', $message);
    }

    public function unassign(string $id, Request $request, BackendApi $backendApi): RedirectResponse
    {
        $data = $request->validate([
            'subjectId' => ['required', 'string', 'max:200'],
        ]);

        $resp = $backendApi->request('POST', "/api/teachers/{$id}/unassign", $data);
        if ($resp->successful()) {
            return back()->with('notice', 'Subject unassigned from teacher.');
        }

        $payload = $resp->json();
        $message = is_array($payload) ? ($payload['error'] ?? 'Backend error') : 'Backend error';
        return back()->with('error', $message);
    }
}
