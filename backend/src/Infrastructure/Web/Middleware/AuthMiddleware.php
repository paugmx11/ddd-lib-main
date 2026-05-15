<?php

declare(strict_types=1);

namespace App\Infrastructure\Web\Middleware;

use App\Infrastructure\Web\Auth\UserTokenAuthenticator;

final class AuthMiddleware
{
    public function __construct(private UserTokenAuthenticator $authenticator)
    {
    }

    /**
     * Perform authentication check for a route.
     * Returns true if request may continue, false if middleware already sent response.
     */
    public function handle(bool $isPublic): bool
    {
        if ($isPublic) {
            return true;
        }

        if ($this->authenticator->isAuthenticated()) {
            return true;
        }

        http_response_code(401);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['error' => 'Unauthorized'], JSON_UNESCAPED_SLASHES);
        return false;
    }
}
