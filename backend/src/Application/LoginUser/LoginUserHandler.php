<?php

declare(strict_types=1);

namespace App\Application\LoginUser;

use App\Domain\User\User;
use App\Domain\User\UserRepository;

final class LoginUserHandler
{
    public function __construct(
        private UserRepository $userRepository
    ) {}

    public function handle(LoginUserCommand $command): User
    {
        $user = $this->userRepository->findByEmail($command->email);
        if ($user === null || !$user->verifyPassword($command->password)) {
            throw new \RuntimeException('Invalid credentials');
        }

        return $user;
    }
}
