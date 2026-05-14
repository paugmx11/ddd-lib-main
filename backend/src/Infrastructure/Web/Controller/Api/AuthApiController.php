<?php

declare(strict_types=1);

namespace App\Infrastructure\Web\Controller\Api;

use App\Application\LoginUser\LoginUserCommand;
use App\Application\LoginUser\LoginUserHandler;
use App\Application\RegisterUser\RegisterUserCommand;
use App\Application\RegisterUser\RegisterUserHandler;
use App\Application\LoginWithGoogle\LoginWithGoogleHandler;
use App\Application\LoginWithGoogle\LoginWithGoogleCommand;
use App\Domain\User\User;
use App\Domain\User\UserId;
use App\Domain\User\UserToken;
use App\Domain\User\UserTokenRepository;
use App\Infrastructure\Web\Auth\BearerTokenExtractor;
use App\Infrastructure\Web\Http\JsonHttpResponder;

final class AuthApiController
{
    use JsonHttpResponder;

    public function __construct(
        private RegisterUserHandler $registerUserHandler,
        private LoginUserHandler $loginUserHandler,
        private UserTokenRepository $userTokenRepository,
        private BearerTokenExtractor $bearerTokenExtractor
        , private ?LoginWithGoogleHandler $loginWithGoogleHandler = null
    ) {}

    public function exchangeGoogleCode(): void
    {
        if ($this->loginWithGoogleHandler === null) {
            $this->jsonResponse(['error' => 'Not implemented'], 501);
            return;
        }

        $data = $this->readJsonBody();
        if ($data === null) {
            return;
        }

        $code = trim((string) ($data['code'] ?? ''));
        if ($code === '') {
            $this->jsonResponse(['error' => 'code is required'], 422);
            return;
        }

        try {
            $user = $this->loginWithGoogleHandler->handle(new LoginWithGoogleCommand($code));
        } catch (\RuntimeException $e) {
            $this->jsonResponse(['error' => $e->getMessage()], 400);
            return;
        }

        $token = $this->issueToken($user);

        $this->jsonResponse([
            'token' => $token,
            'user' => $this->serializeUser($user),
        ], 200);
    }

    public function register(): void
    {
        $data = $this->readJsonBody();
        if ($data === null) {
            return;
        }

        $name = trim((string) ($data['name'] ?? ''));
        $email = trim((string) ($data['email'] ?? ''));
        $password = (string) ($data['password'] ?? '');

        if ($name === '' || $email === '' || $password === '') {
            $this->jsonResponse(['error' => 'name, email and password are required'], 422);
            return;
        }

        try {
            $user = $this->registerUserHandler->handle(
                new RegisterUserCommand(
                    UserId::generate()->value(),
                    $name,
                    $email,
                    $password
                )
            );
        } catch (\RuntimeException $e) {
            $this->jsonResponse(['error' => $e->getMessage()], 409);
            return;
        } catch (\InvalidArgumentException $e) {
            $this->jsonResponse(['error' => $e->getMessage()], 422);
            return;
        }

        $token = $this->issueToken($user);

        $this->jsonResponse([
            'token' => $token,
            'user' => $this->serializeUser($user),
        ], 201);
    }

    public function login(): void
    {
        $data = $this->readJsonBody();
        if ($data === null) {
            return;
        }

        $email = trim((string) ($data['email'] ?? ''));
        $password = (string) ($data['password'] ?? '');

        if ($email === '' || $password === '') {
            $this->jsonResponse(['error' => 'email and password are required'], 422);
            return;
        }

        try {
            $user = $this->loginUserHandler->handle(
                new LoginUserCommand($email, $password)
            );
        } catch (\RuntimeException $e) {
            $this->jsonResponse(['error' => $e->getMessage()], 401);
            return;
        }

        $token = $this->issueToken($user);

        $this->jsonResponse([
            'token' => $token,
            'user' => $this->serializeUser($user),
        ], 200);
    }

    public function logout(): void
    {
        $token = $this->bearerTokenExtractor->extractFromServerGlobals();
        if ($token === null) {
            $this->jsonResponse(['error' => 'Unauthorized'], 401);
            return;
        }

        $userToken = $this->userTokenRepository->find($token);
        if ($userToken === null) {
            $this->jsonResponse(['error' => 'Unauthorized'], 401);
            return;
        }

        $this->userTokenRepository->deleteByUser($userToken->userId());
        $this->jsonResponse(['message' => 'Logged out'], 200);
    }

    private function issueToken(User $user): string
    {
        $this->userTokenRepository->deleteByUser($user->id());

        $token = bin2hex(random_bytes(32));
        $this->userTokenRepository->save(new UserToken($token, $user->id()));

        return $token;
    }

    private function serializeUser(User $user): array
    {
        return [
            'id' => $user->id()->value(),
            'name' => $user->name(),
            'email' => $user->email(),
        ];
    }
}
