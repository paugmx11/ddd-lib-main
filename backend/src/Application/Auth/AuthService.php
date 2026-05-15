<?php

declare(strict_types=1);

namespace App\Application\Auth;

use App\Domain\User\User;
use App\Domain\User\UserToken;
use App\Domain\User\UserTokenRepository;
use App\Domain\User\UserId;

final class AuthService
{
    public function __construct(private UserTokenRepository $userTokenRepository)
    {
    }

    public function issueToken(User $user): string
    {
        $this->userTokenRepository->deleteByUser($user->id());

        $token = bin2hex(random_bytes(32));
        $this->userTokenRepository->save(new UserToken($token, $user->id()));

        return $token;
    }

    public function invalidateTokensForUser(UserId $userId): void
    {
        $this->userTokenRepository->deleteByUser($userId);
    }
}
