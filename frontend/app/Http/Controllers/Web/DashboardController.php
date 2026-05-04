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
            'courses' => null,
            'students' => null,
            'teachers' => null,
            'subjects' => null,
        ];

        try {
            $courses = $backendApi->request('GET', '/api/courses');
            if ($courses->successful() && is_array($courses->json())) {
                $counts['courses'] = count($courses->json());
            }

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
        ]);
    }
}
