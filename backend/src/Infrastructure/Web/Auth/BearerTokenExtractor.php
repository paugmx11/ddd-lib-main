<?php

declare(strict_types=1);

namespace App\Infrastructure\Web\Auth;

final class BearerTokenExtractor
{
    public function extractFromServerGlobals(): ?string
    {
        $authHeader = (string) ($_SERVER['HTTP_AUTHORIZATION'] ?? '');

        return $this->extractFromHeader($authHeader);
    }

    public function extractFromHeader(string $authorizationHeader): ?string
    {
        if (!str_starts_with($authorizationHeader, 'Bearer ')) {
            return null;
        }

        $token = trim(substr($authorizationHeader, 7));

        return $token === '' ? null : $token;
    }
}

