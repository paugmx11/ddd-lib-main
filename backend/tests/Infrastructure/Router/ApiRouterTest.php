<?php

declare(strict_types=1);

namespace Tests\Infrastructure\Router;

use PHPUnit\Framework\TestCase;
use App\Infrastructure\Web\Router\ApiRouter;
use App\Infrastructure\Web\Auth\UserTokenAuthenticator;
use App\Infrastructure\Web\Auth\BearerTokenExtractor;
use App\Domain\User\UserTokenRepository;
use App\Domain\User\UserToken;
use App\Domain\User\UserId;

class ApiRouterTest extends TestCase
{
    public function tearDown(): void
    {
        // clear any header/server state modified during tests
        unset($_SERVER['HTTP_AUTHORIZATION']);
        http_response_code(200);
        parent::tearDown();
    }

    public function test_public_route_is_called_even_if_not_authenticated(): void
    {
        $routes = [
            ['GET', '#^/api/ping$#', 'testApi.ping', true],
        ];

        $controller = new class {
            public function ping(): void
            {
                echo 'pong';
            }
        };

        $controllers = ['testApi' => $controller];

        $fakeRepo = new class implements UserTokenRepository {
            public function find(string $token): ?UserToken { return null; }
            public function save(UserToken $userToken): void {}
            public function deleteByUser(UserId $userId): void {}
        };

        $authenticator = new UserTokenAuthenticator($fakeRepo, new BearerTokenExtractor());

        $router = new ApiRouter($routes, $controllers, $authenticator);

        ob_start();
        $router->dispatch('GET', '/api/ping');
        $out = ob_get_clean();

        $this->assertSame('pong', $out);
    }

    public function test_private_route_denies_when_not_authenticated_and_allows_when_authenticated(): void
    {
        $routes = [
            ['GET', '#^/api/private$#', 'testApi.private'],
        ];

        $controller = new class {
            public function private(): void
            {
                echo 'private-ok';
            }
        };

        $controllers = ['testApi' => $controller];

        // repo that never finds tokens
        $noRepo = new class implements UserTokenRepository {
            public function find(string $token): ?UserToken { return null; }
            public function save(UserToken $userToken): void {}
            public function deleteByUser(UserId $userId): void {}
        };

        $authenticatorNo = new UserTokenAuthenticator($noRepo, new BearerTokenExtractor());
        $routerNo = new ApiRouter($routes, $controllers, $authenticatorNo);

        // ensure no auth header
        unset($_SERVER['HTTP_AUTHORIZATION']);

        ob_start();
        $routerNo->dispatch('GET', '/api/private');
        $out = ob_get_clean();

        $data = json_decode($out, true);
        $this->assertIsArray($data);
        $this->assertSame('Unauthorized', $data['error']);
        $this->assertSame(401, http_response_code());

        // now authenticated
        $token = 'good-token';
        $userToken = new UserToken($token, UserId::generate());

        $yesRepo = new class($userToken) implements UserTokenRepository {
            private UserToken $stored;
            public function __construct(UserToken $t) { $this->stored = $t; }
            public function find(string $token): ?UserToken { return $this->stored->token() === $token ? $this->stored : null; }
            public function save(UserToken $userToken): void {}
            public function deleteByUser(UserId $userId): void {}
        };

        // set Authorization header
        $_SERVER['HTTP_AUTHORIZATION'] = 'Bearer ' . $token;

        $authenticatorYes = new UserTokenAuthenticator($yesRepo, new BearerTokenExtractor());
        $routerYes = new ApiRouter($routes, $controllers, $authenticatorYes);

        ob_start();
        $routerYes->dispatch('GET', '/api/private');
        $out2 = ob_get_clean();

        $this->assertSame('private-ok', $out2);
    }
}
