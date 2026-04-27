<?php

declare(strict_types=1);

namespace App\Http\Controllers\ClientApi;

use App\Services\BackendApi;
use Illuminate\Http\Request;

final class CoursesController extends BaseProxyController
{
    public function __construct(
        private readonly BackendApi $backendApi
    ) {}

    public function index()
    {
        if ($r = $this->requireBackendToken()) return $r;
        return $this->proxyJson($this->backendApi->request('GET', '/api/courses'));
    }

    public function show(string $id)
    {
        if ($r = $this->requireBackendToken()) return $r;
        return $this->proxyJson($this->backendApi->request('GET', "/api/courses/{$id}"));
    }

    public function store(Request $request)
    {
        if ($r = $this->requireBackendToken()) return $r;
        $data = $request->validate([
            'name' => ['required', 'string', 'max:200'],
            'startDate' => ['required', 'date_format:Y-m-d'],
            'endDate' => ['required', 'date_format:Y-m-d'],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);

        return $this->proxyJson($this->backendApi->request('POST', '/api/courses', $data));
    }

    public function update(Request $request, string $id)
    {
        if ($r = $this->requireBackendToken()) return $r;
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:200'],
            'startDate' => ['sometimes', 'date_format:Y-m-d'],
            'endDate' => ['sometimes', 'date_format:Y-m-d'],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);

        return $this->proxyJson($this->backendApi->request('PUT', "/api/courses/{$id}", $data));
    }

    public function destroy(string $id)
    {
        if ($r = $this->requireBackendToken()) return $r;
        return $this->proxyJson($this->backendApi->request('DELETE', "/api/courses/{$id}"));
    }
}
