<?php

declare(strict_types=1);

namespace App\Infrastructure\Web\Router;

use App\Infrastructure\Web\Auth\UserTokenAuthenticator;

final class ApiRouter
{
    /**
     * @param array<int, array{0:string,1:string,2:string,3?:bool}> $routes
     * @param array<string, object> $controllers
     */
    public function __construct(
        private array $routes,
        private array $controllers,
        private UserTokenAuthenticator $authenticator
    ) {}

    public function dispatch(string $method, string $path): void
    {
        $allowedMethods = [];

        foreach ($this->routes as $route) {
            [$routeMethod, $pattern, $handler] = $route;
            $isPublic = (bool) ($route[3] ?? false);

            if (!preg_match($pattern, $path, $matches)) {
                continue;
            }

            $allowedMethods[] = $routeMethod;

            if (strtoupper($method) !== $routeMethod) {
                continue;
            }

            if (!$isPublic && !$this->authenticator->isAuthenticated()) {
                $this->jsonError('Unauthorized', 401);
                return;
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
        echo json_encode(['error' => $message], JSON_UNESCAPED_SLASHES);
    }
}
