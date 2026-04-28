<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Services\BackendApi;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class SubjectsPageController
{
    public function index(BackendApi $backendApi): View
    {
        $subjects = [];
        $courses = [];
        $courseNameById = [];
        $teachers = [];
        $teacherNameById = [];
        $error = '';

        try {
            $resp = $backendApi->request('GET', '/api/subjects');
            if ($resp->successful() && is_array($resp->json())) {
                $subjects = $resp->json();
            } else {
                $payload = $resp->json();
                $error = is_array($payload) ? (string) ($payload['error'] ?? 'Backend error') : 'Backend error';
            }

            $cr = $backendApi->request('GET', '/api/courses');
            if ($cr->successful() && is_array($cr->json())) {
                $courses = $cr->json();
                foreach ($courses as $c) {
                    if (is_array($c) && isset($c['id'], $c['name'])) {
                        $courseNameById[(string) $c['id']] = (string) $c['name'];
                    }
                }
            }

            $tr = $backendApi->request('GET', '/api/teachers');
            if ($tr->successful() && is_array($tr->json())) {
                $teachers = $tr->json();
                foreach ($teachers as $t) {
                    if (is_array($t) && isset($t['id'], $t['name'])) {
                        $teacherNameById[(string) $t['id']] = (string) $t['name'];
                    }
                }
            }
        } catch (\Throwable) {
            $error = 'Cannot reach backend';
        }

        if ($error !== '') {
            session()->flash('error', $error);
        }

        return view('subjects.index', [
            'subjects' => $subjects,
            'courses' => $courses,
            'courseNameById' => $courseNameById,
            'teacherNameById' => $teacherNameById,
        ]);
    }

    public function store(Request $request, BackendApi $backendApi): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:200'],
            'courseId' => ['required', 'string', 'max:200'],
        ]);

        $resp = $backendApi->request('POST', '/api/subjects', $data);
        if (!$resp->successful()) {
            $payload = $resp->json();
            return back()->withInput()->with('error', is_array($payload) ? ($payload['error'] ?? 'Backend error') : 'Backend error');
        }

        return redirect('/subjects')->with('notice', 'Subject created.');
    }

    public function destroy(string $id, BackendApi $backendApi): RedirectResponse
    {
        $resp = $backendApi->request('DELETE', "/api/subjects/{$id}");
        if (!$resp->successful() && $resp->status() !== 204) {
            $payload = $resp->json();
            return back()->with('error', is_array($payload) ? ($payload['error'] ?? 'Backend error') : 'Backend error');
        }

        return redirect('/subjects')->with('notice', 'Subject deleted.');
    }
}
