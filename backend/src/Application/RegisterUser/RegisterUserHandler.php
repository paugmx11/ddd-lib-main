<?php

declare(strict_types=1);

namespace App\Application\RegisterUser;

use App\Domain\User\User;
use App\Domain\User\UserId;
use App\Domain\User\UserRepository;

final class RegisterUserHandler
{
    public function __construct(
        private UserRepository $userRepository
    ) {}

    public function handle(RegisterUserCommand $command): User
    {
        $existingUser = $this->userRepository->findByEmail($command->email);
        if ($existingUser !== null) {
            throw new \RuntimeException('A user with this email already exists');
        }

        if (strlen($command->password) < 4) {
            throw new \InvalidArgumentException('Password must be at least 4 characters');
        }

        $user = new User(
            new UserId($command->userId),
            $command->name,
            $command->email,
            password_hash($command->password, PASSWORD_DEFAULT)
        );

        $this->userRepository->save($user);

        return $user;
    }
}
