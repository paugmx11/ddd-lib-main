<?php

declare(strict_types=1);

namespace App\Http\Controllers\ClientApi;

use App\Services\BackendApi;
use Illuminate\Http\Request;

final class SubjectsController extends BaseProxyController
{
    public function __construct(
        private readonly BackendApi $backendApi
    ) {}

    public function index()
    {
        if ($r = $this->requireBackendToken()) return $r;
        return $this->proxyJson($this->backendApi->request('GET', '/api/subjects'));
    }

    public function show(string $id)
    {
        if ($r = $this->requireBackendToken()) return $r;
        return $this->proxyJson($this->backendApi->request('GET', "/api/subjects/{$id}"));
    }

    public function store(Request $request)
    {
        if ($r = $this->requireBackendToken()) return $r;
        $data = $request->validate([
            'name' => ['required', 'string', 'max:200'],
            'courseId' => ['required', 'string', 'max:200'],
        ]);

        return $this->proxyJson($this->backendApi->request('POST', '/api/subjects', $data));
    }

    public function update(Request $request, string $id)
    {
        if ($r = $this->requireBackendToken()) return $r;
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:200'],
            'courseId' => ['sometimes', 'string', 'max:200'],
        ]);

        return $this->proxyJson($this->backendApi->request('PUT', "/api/subjects/{$id}", $data));
    }

    public function destroy(string $id)
    {
        if ($r = $this->requireBackendToken()) return $r;
        return $this->proxyJson($this->backendApi->request('DELETE', "/api/subjects/{$id}"));
    }
}
