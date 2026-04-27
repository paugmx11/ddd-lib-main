<?php

declare(strict_types=1);

namespace App\Infrastructure\Web\Router;

use App\Domain\User\UserTokenRepository;

final class ApiRouter
{
    /**
     * @param array<int, array{0:string,1:string,2:string}> $routes
     * @param array<string, object> $controllers
     */
    public function __construct(
        private array $routes,
        private array $controllers,
        private UserTokenRepository $userTokenRepository
    ) {}

    public function dispatch(string $method, string $path): void
    {
        if (!$this->authenticate($path)) {
            return;
        }

        $allowedMethods = [];

        foreach ($this->routes as $route) {
            [$routeMethod, $pattern, $handler] = $route;

            if (!preg_match($pattern, $path, $matches)) {
                continue;
            }

            $allowedMethods[] = $routeMethod;

            if (strtoupper($method) !== $routeMethod) {
                continue;
            }

            [$controllerKey, $action] = explode('.', $handler, 2);
            $controller = $this->controllers[$controllerKey] ?? null;

            if ($controller === null || !method_exists($controller, $action)) {
                $this->jsonError('Route controller/action not configured correctly', 500);
                return;
            }

            array_shift($matches);
            $controller->{$action}(...$matches);
            return;
        }

        if ($allowedMethods !== []) {
            http_response_code(405);
            header('Allow: ' . implode(', ', array_unique($allowedMethods)));
            $this->jsonError('Method Not Allowed', 405);
            return;
        }

        $this->jsonError('Not Found', 404);
    }

    private function jsonError(string $message, int $status): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['error' => $message]);
    }

    private function authenticate(string $path): bool
    {
        if (str_starts_with($path, '/api/auth/')) {
            return true;
        }

        $authHeader = (string) ($_SERVER['HTTP_AUTHORIZATION'] ?? '');
        if (!str_starts_with($authHeader, 'Bearer ')) {
            $this->jsonError('Unauthorized', 401);
            return false;
        }

        $token = trim(substr($authHeader, 7));
        if ($token === '') {
            $this->jsonError('Unauthorized', 401);
            return false;
        }

        $userToken = $this->userTokenRepository->find($token);
        if ($userToken === null) {
            $this->jsonError('Unauthorized', 401);
            return false;
        }

        $_SERVER['AUTH_USER_ID'] = $userToken->userId()->value();

        return true;
    }
}
