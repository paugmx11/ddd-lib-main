<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Services\BackendApi;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

final class DashboardController
{
    public function __invoke(BackendApi $backendApi): View
    {
        $counts = [
            'students' => null,
            'teachers' => null,
            'subjects' => null,
        ];

        try {
            $students = $backendApi->request('GET', '/api/students');
            if ($students->successful() && is_array($students->json())) {
                $counts['students'] = count($students->json());
            }

            $teachers = $backendApi->request('GET', '/api/teachers');
            if ($teachers->successful() && is_array($teachers->json())) {
                $counts['teachers'] = count($teachers->json());
            }

            $subjects = $backendApi->request('GET', '/api/subjects');
            if ($subjects->successful() && is_array($subjects->json())) {
                $counts['subjects'] = count($subjects->json());
            }
        } catch (\Throwable) {
            // Best-effort counts.
        }

        return view('dashboard', [
            'counts' => $counts,
            'userEmail' => Auth::user()?->email ?? '',
            'hasBackendToken' => (string) session('backend_token', '') !== '',
        ]);
    }
}

