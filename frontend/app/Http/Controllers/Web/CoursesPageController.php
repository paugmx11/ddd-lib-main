<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Services\BackendApi;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class CoursesPageController
{
    public function index(BackendApi $backendApi): View
    {
        $courses = [];
        $error = '';

        try {
            $resp = $backendApi->request('GET', '/api/courses');
            if ($resp->successful() && is_array($resp->json())) {
                $courses = $resp->json();
            } else {
                $payload = $resp->json();
                $error = is_array($payload) ? (string) ($payload['error'] ?? 'Backend error') : 'Backend error';
            }
        } catch (\Throwable) {
            $error = 'Cannot reach backend';
        }

        if ($error !== '') {
            session()->flash('error', $error);
        }

        return view('courses.index', [
            'courses' => $courses,
        ]);
    }

    public function store(Request $request, BackendApi $backendApi): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:200'],
            'startDate' => ['required', 'date'],
            'endDate' => ['required', 'date'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $resp = $backendApi->request('POST', '/api/courses', $data);
        if (!$resp->successful()) {
            $payload = $resp->json();
            return back()->withInput()->with('error', is_array($payload) ? ($payload['error'] ?? 'Backend error') : 'Backend error');
        }

        return redirect('/courses')->with('notice', 'Course created.');
    }

    public function destroy(string $id, BackendApi $backendApi): RedirectResponse
    {
        $resp = $backendApi->request('DELETE', "/api/courses/{$id}");
        if (!$resp->successful() && $resp->status() !== 204) {
            $payload = $resp->json();
            return back()->with('error', is_array($payload) ? ($payload['error'] ?? 'Backend error') : 'Backend error');
        }

        return redirect('/courses')->with('notice', 'Course deleted.');
    }
}

