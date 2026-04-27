<?php

declare(strict_types=1);

namespace App\Http\Controllers\ClientApi;

use Illuminate\Http\Client\Response as ClientResponse;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

abstract class BaseProxyController
{
    protected function requireBackendToken(): ?JsonResponse
    {
        $token = (string) session('backend_token', '');
        if ($token === '') {
            return response()->json([
                'error' => 'Missing backend token. Logout and login again.',
            ], 401);
        }

        return null;
    }

    protected function proxyJson(ClientResponse $resp): Response
    {
        if ($resp->status() === 204) {
            return response('', 204);
        }

        $contentType = $resp->header('Content-Type');
        $payload = $resp->json();

        // If backend didn't return JSON (or body is empty), pass raw through.
        if ($payload === null && trim($resp->body()) !== '') {
            $r = response($resp->body(), $resp->status());
            if (is_string($contentType) && $contentType !== '') {
                $r->header('Content-Type', $contentType);
            }
            return $r;
        }

        return response()->json($payload, $resp->status());
    }
}
