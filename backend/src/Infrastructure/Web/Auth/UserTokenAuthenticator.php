<?php

declare(strict_types=1);

namespace App\Infrastructure\Web\Auth;

use App\Domain\User\UserTokenRepository;

final class UserTokenAuthenticator
{
    public function __construct(
        private UserTokenRepository $userTokenRepository,
        private BearerTokenExtractor $bearerTokenExtractor
    ) {}

    public function isAuthenticated(): bool
    {
        $token = $this->bearerTokenExtractor->extractFromServerGlobals();
        if ($token === null) {
            return false;
        }

        return $this->userTokenRepository->find($token) !== null;
    }
}

