<?php

declare(strict_types=1);

namespace Tests\Infrastructure\Controller;

use PHPUnit\Framework\TestCase;
use App\Infrastructure\Web\Controller\Api\AuthApiController;
use App\Application\RegisterUser\RegisterUserHandler;
use App\Application\LoginUser\LoginUserHandler;
use App\Application\Auth\AuthService;
use App\Infrastructure\Web\Auth\BearerTokenExtractor;
use App\Domain\User\UserRepository;
use App\Domain\User\UserTokenRepository;
use App\Domain\User\User;
use App\Domain\User\UserId;
use App\Domain\User\UserToken;

final class AuthApiControllerTest extends TestCase
{
    private AuthApiController $controller;
    private InMemoryUserRepository $userRepo;
    private InMemoryUserTokenRepository $tokenRepo;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userRepo = new InMemoryUserRepository();
        $this->tokenRepo = new InMemoryUserTokenRepository();

        $register = new RegisterUserHandler($this->userRepo);
        $login = new LoginUserHandler($this->userRepo);
        $authService = new AuthService($this->tokenRepo);

        $this->controller = new AuthApiController(
            $register,
            $login,
            $this->tokenRepo,
            new BearerTokenExtractor(),
            $authService,
            null
        );
    }

    protected function tearDown(): void
    {
        header_remove();
        http_response_code(200);
        unset($_SERVER['HTTP_AUTHORIZATION'], $_SERVER['MOCK_JSON_BODY']);
        parent::tearDown();
    }

    public function test_register_returns_token_and_user(): void
    {
        $resp = $this->runJson(function (): void {
            $this->controller->register();
        }, [
            'name' => 'Tester',
            'email' => 't@example.com',
            'password' => 'secret123',
        ]);

        $this->assertSame(201, $resp['status']);
        $this->assertIsArray($resp['json']);
        $this->assertArrayHasKey('token', $resp['json']);
        $this->assertIsString($resp['json']['token']);
        $this->assertNotSame('', $resp['json']['token']);
        $this->assertSame('t@example.com', $resp['json']['user']['email']);
        $this->assertNotNull($this->tokenRepo->find($resp['json']['token']));
    }

    public function test_login_returns_token_for_existing_user(): void
    {
        // Register first (to create user in repository)
        $this->runJson(fn () => $this->controller->register(), [
            'name' => 'Tester',
            'email' => 't@example.com',
            'password' => 'secret123',
        ]);

        $resp = $this->runJson(function (): void {
            $this->controller->login();
        }, [
            'email' => 't@example.com',
            'password' => 'secret123',
        ]);

        $this->assertSame(200, $resp['status']);
        $this->assertIsArray($resp['json']);
        $this->assertArrayHasKey('token', $resp['json']);
        $this->assertNotSame('', (string) $resp['json']['token']);
    }

    public function test_logout_requires_bearer_token_and_invalidates(): void
    {
        $register = $this->runJson(fn () => $this->controller->register(), [
            'name' => 'Tester',
            'email' => 't@example.com',
            'password' => 'secret123',
        ]);

        $token = (string) $register['json']['token'];
        $this->assertNotNull($this->tokenRepo->find($token));

        $_SERVER['HTTP_AUTHORIZATION'] = 'Bearer ' . $token;

        $resp = $this->runRaw(function (): void {
            $this->controller->logout();
        });

        $this->assertSame(200, $resp['status']);
        $json = json_decode($resp['body'], true);
        $this->assertIsArray($json);
        $this->assertSame('Logged out', $json['message'] ?? null);
        $this->assertNull($this->tokenRepo->find($token));
    }

    public function test_logout_without_token_returns_401(): void
    {
        unset($_SERVER['HTTP_AUTHORIZATION']);

        $resp = $this->runRaw(function (): void {
            $this->controller->logout();
        });

        $this->assertSame(401, $resp['status']);
        $json = json_decode($resp['body'], true);
        $this->assertIsArray($json);
        $this->assertSame('Unauthorized', $json['error'] ?? null);
    }

    /**
     * @param array<string, mixed> $body
     * @return array{status:int, json: mixed, body: string}
     */
    private function runJson(callable $fn, array $body): array
    {
        $raw = json_encode($body, JSON_UNESCAPED_SLASHES);
        if (!is_string($raw)) {
            $this->fail('Unable to encode request JSON');
        }

        $_SERVER['MOCK_JSON_BODY'] = $raw;

        $resp = $this->runRaw($fn);

        return [
            'status' => $resp['status'],
            'body' => $resp['body'],
            'json' => json_decode($resp['body'], true),
        ];
    }

    /**
     * @return array{status:int, body:string}
     */
    private function runRaw(callable $fn): array
    {
        header_remove();
        http_response_code(200);

        ob_start();
        $fn();
        $body = (string) ob_get_clean();

        return [
            'status' => (int) http_response_code(),
            'body' => $body,
        ];
    }
}

final class InMemoryUserRepository implements UserRepository
{
    /** @var array<string, User> */
    private array $byId = [];
    /** @var array<string, User> */
    private array $byEmail = [];

    public function find(UserId $id): ?User
    {
        return $this->byId[$id->value()] ?? null;
    }

    public function findByEmail(string $email): ?User
    {
        return $this->byEmail[strtolower($email)] ?? null;
    }

    public function save(User $user): void
    {
        $this->byId[$user->id()->value()] = $user;
        $this->byEmail[strtolower($user->email())] = $user;
    }
}

final class InMemoryUserTokenRepository implements UserTokenRepository
{
    /** @var array<string, UserToken> */
    private array $tokens = [];

    public function find(string $token): ?UserToken
    {
        return $this->tokens[$token] ?? null;
    }

    public function save(UserToken $userToken): void
    {
        $this->tokens[$userToken->token()] = $userToken;
    }

    public function deleteByUser(UserId $userId): void
    {
        foreach ($this->tokens as $token => $userToken) {
            if ($userToken->userId()->value() === $userId->value()) {
                unset($this->tokens[$token]);
            }
        }
    }
}

