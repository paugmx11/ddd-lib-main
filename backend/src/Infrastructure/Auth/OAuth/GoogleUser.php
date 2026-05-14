<?php

declare(strict_types=1);

namespace App\Infrastructure\Auth\OAuth;

final class GoogleUser
{
    public function __construct(
        public readonly string $id,
        public readonly string $email,
        public readonly string $name
    ) {}
}
