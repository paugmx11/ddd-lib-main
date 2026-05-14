<?php

declare(strict_types=1);

namespace App\Application\LoginWithGoogle;

use App\Infrastructure\Auth\OAuth\GoogleOAuthClientInterface;
use App\Domain\User\UserRepository;
use App\Domain\User\UserId;
use App\Domain\User\User;
use App\Domain\User\PasswordHash;

final class LoginWithGoogleHandler
{
    public function __construct(
        private GoogleOAuthClientInterface $googleClient,
        private UserRepository $userRepository
    ) {
    }

    public function handle(LoginWithGoogleCommand $command): User
    {
        $googleUser = $this->googleClient->fetchUserFromAuthorizationCode($command->code);

        $existing = $this->userRepository->findByEmail($googleUser->email);
        if ($existing !== null) {
            return $existing;
        }

        // create a new user with a random id and a random password hash (OAuth users won't use it)
        $id = UserId::generate();
        $randomHash = password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT);
        $user = new User($id, $googleUser->name, $googleUser->email, $randomHash);
        $this->userRepository->save($user);

        return $user;
    }
}
