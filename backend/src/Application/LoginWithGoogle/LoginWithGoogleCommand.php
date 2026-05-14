<?php

declare(strict_types=1);

namespace App\Application\LoginWithGoogle;

final class LoginWithGoogleCommand
{
    public function __construct(public readonly string $code)
    {
    }
}
