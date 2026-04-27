<?php

declare(strict_types=1);

namespace App\Http\Controllers\ClientApi;

use App\Services\BackendApi;
use Illuminate\Http\Request;

final class TeachersController extends BaseProxyController
{
    public function __construct(
        private readonly BackendApi $backendApi
    ) {}

    public function index()
    {
        if ($r = $this->requireBackendToken()) return $r;
        return $this->proxyJson($this->backendApi->request('GET', '/api/teachers'));
    }

    public function show(string $id)
    {
        if ($r = $this->requireBackendToken()) return $r;
        return $this->proxyJson($this->backendApi->request('GET', "/api/teachers/{$id}"));
    }

    public function store(Request $request)
    {
        if ($r = $this->requireBackendToken()) return $r;
        $data = $request->validate([
            'name' => ['required', 'string', 'max:200'],
            'email' => ['required', 'email', 'max:200'],
        ]);

        return $this->proxyJson($this->backendApi->request('POST', '/api/teachers', $data));
    }

    public function update(Request $request, string $id)
    {
        if ($r = $this->requireBackendToken()) return $r;
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:200'],
            'email' => ['sometimes', 'email', 'max:200'],
        ]);

        return $this->proxyJson($this->backendApi->request('PUT', "/api/teachers/{$id}", $data));
    }

    public function destroy(string $id)
    {
        if ($r = $this->requireBackendToken()) return $r;
        return $this->proxyJson($this->backendApi->request('DELETE', "/api/teachers/{$id}"));
    }

    public function assign(Request $request, string $id)
    {
        if ($r = $this->requireBackendToken()) return $r;
        $data = $request->validate([
            'subjectId' => ['required', 'string', 'max:200'],
        ]);

        return $this->proxyJson($this->backendApi->request('POST', "/api/teachers/{$id}/assign", $data));
    }

    public function unassign(Request $request, string $id)
    {
        if ($r = $this->requireBackendToken()) return $r;
        $data = $request->validate([
            'subjectId' => ['required', 'string', 'max:200'],
        ]);

        return $this->proxyJson($this->backendApi->request('POST', "/api/teachers/{$id}/unassign", $data));
    }
}
