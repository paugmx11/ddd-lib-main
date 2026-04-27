<?php

declare(strict_types=1);

namespace App\Infrastructure\Web\Controller\Api;

use App\Application\LoginUser\LoginUserCommand;
use App\Application\LoginUser\LoginUserHandler;
use App\Application\RegisterUser\RegisterUserCommand;
use App\Application\RegisterUser\RegisterUserHandler;
use App\Domain\User\User;
use App\Domain\User\UserId;
use App\Domain\User\UserToken;
use App\Domain\User\UserTokenRepository;

final class AuthApiController
{
    public function __construct(
        private RegisterUserHandler $registerUserHandler,
        private LoginUserHandler $loginUserHandler,
        private UserTokenRepository $userTokenRepository
    ) {}

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
        $token = $this->extractBearerToken();
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

    private function extractBearerToken(): ?string
    {
        $header = (string) ($_SERVER['HTTP_AUTHORIZATION'] ?? '');
        if (!str_starts_with($header, 'Bearer ')) {
            return null;
        }

        $token = trim(substr($header, 7));

        return $token === '' ? null : $token;
    }

    private function readJsonBody(): ?array
    {
        $raw = (string) file_get_contents('php://input');
        if ($raw === '') {
            $this->jsonResponse(['error' => 'Empty request body'], 400);
            return null;
        }

        $data = json_decode($raw, true);
        if (!is_array($data)) {
            $this->jsonResponse(['error' => 'Invalid JSON body'], 400);
            return null;
        }

        return $data;
    }

    private function jsonResponse(array $payload, int $status): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload);
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
