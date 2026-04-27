<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

final class BackendApi
{
    public function request(
        string $method,
        string $path,
        array $jsonBody = [],
        bool $withAuth = true
    ): Response {
        $client = Http::baseUrl(rtrim((string) config('backend.base_url'), '/'))
            ->timeout((int) config('backend.timeout_seconds', 10))
            ->acceptJson();

        if ($withAuth) {
            $token = (string) session('backend_token', '');
            if ($token !== '') {
                $client = $client->withToken($token);
            }
        }

        $method = strtoupper($method);
        $path = '/' . ltrim($path, '/');

        if (in_array($method, ['GET', 'DELETE'], true)) {
            return $client->send($method, $path);
        }

        return $client->send($method, $path, ['json' => $jsonBody]);
    }
}

