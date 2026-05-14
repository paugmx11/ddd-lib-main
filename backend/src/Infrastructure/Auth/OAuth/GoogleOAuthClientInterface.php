<?php

declare(strict_types=1);

namespace App\Infrastructure\Auth\OAuth;

interface GoogleOAuthClientInterface
{
    public function fetchUserFromAuthorizationCode(string $code, ?string $redirectUriOverride = null): GoogleUser;
}
