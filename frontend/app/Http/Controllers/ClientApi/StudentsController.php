<?php

declare(strict_types=1);

namespace App\Http\Controllers\ClientApi;

use App\Services\BackendApi;
use Illuminate\Http\Request;

final class StudentsController extends BaseProxyController
{
    public function __construct(
        private readonly BackendApi $backendApi
    ) {}

    public function index()
    {
        if ($r = $this->requireBackendToken()) return $r;
        return $this->proxyJson($this->backendApi->request('GET', '/api/students'));
    }

    public function show(string $id)
    {
        if ($r = $this->requireBackendToken()) return $r;
        return $this->proxyJson($this->backendApi->request('GET', "/api/students/{$id}"));
    }

    public function store(Request $request)
    {
        if ($r = $this->requireBackendToken()) return $r;
        $data = $request->validate([
            'name' => ['required', 'string', 'max:200'],
            'email' => ['required', 'email', 'max:200'],
        ]);

        return $this->proxyJson($this->backendApi->request('POST', '/api/students', $data));
    }

    public function update(Request $request, string $id)
    {
        if ($r = $this->requireBackendToken()) return $r;
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:200'],
            'email' => ['sometimes', 'email', 'max:200'],
        ]);

        return $this->proxyJson($this->backendApi->request('PUT', "/api/students/{$id}", $data));
    }

    public function destroy(string $id)
    {
        if ($r = $this->requireBackendToken()) return $r;
        return $this->proxyJson($this->backendApi->request('DELETE', "/api/students/{$id}"));
    }

    public function enroll(Request $request, string $id)
    {
        if ($r = $this->requireBackendToken()) return $r;
        $data = $request->validate([
            'courseId' => ['required', 'string', 'max:200'],
        ]);

        return $this->proxyJson($this->backendApi->request('POST', "/api/students/{$id}/enroll", $data));
    }
}
