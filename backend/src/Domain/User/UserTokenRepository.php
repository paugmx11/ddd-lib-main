<?php

declare(strict_types=1);

namespace App\Domain\User;

interface UserTokenRepository
{
    public function find(string $token): ?UserToken;
    public function save(UserToken $userToken): void;
    public function deleteByUser(UserId $userId): void;
}
