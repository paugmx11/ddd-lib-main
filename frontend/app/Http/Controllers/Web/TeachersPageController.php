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

    public function destroy(string $id, BackendApi $backendApi): RedirectResponse
    {
        $resp = $backendApi->request('DELETE', "/api/teachers/{$id}");
        if (!$resp->successful() && $resp->status() !== 204) {
            $payload = $resp->json();
            return back()->with('error', is_array($payload) ? ($payload['error'] ?? 'Backend error') : 'Backend error');
        }

        return redirect('/teachers')->with('notice', 'Teacher deleted.');
    }
}
