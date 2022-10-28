<?php

declare(strict_types=1);

namespace Iquety\Application\Routing;

use Iquety\Injection\InversionOfControl;
use Psr\Container\ContainerInterface;
use RuntimeException;

/** @SuppressWarnings(PHPMD.TooManyPublicMethods) */
class Router
{
    private ?Route $currentRoute = null;

    /** @var array<int,Route> */
    private array $routes = [];

    private bool $notFound = false;

    private bool $accessDenied = false;

    private string $moduleIdentifier = 'all';

    private ?ContainerInterface $container = null;

    public function any(string $pattern): Route
    {
        return $this->makeRoute(Route::ANY, $pattern);
    }

    public function delete(string $pattern): Route
    {
        return $this->makeRoute(Route::DELETE, $pattern);
    }

    public function get(string $pattern): Route
    {
        return $this->makeRoute(Route::GET, $pattern);
    }

    public function patch(string $pattern): Route
    {
        return $this->makeRoute(Route::PATCH, $pattern);
    }

    public function post(string $pattern): Route
    {
        return $this->makeRoute(Route::POST, $pattern);
    }

    public function put(string $pattern): Route
    {
        return $this->makeRoute(Route::PUT, $pattern);
    }

    /** Obtém a rota atual */
    public function currentRoute(): ?Route
    {
        return $this->currentRoute;
    }

    /** Determina o módulo para o qual as rotas irão pertencer */
    public function forModule(string $moduleIdentifier): Router
    {
        $this->moduleIdentifier = $moduleIdentifier;

        return $this;
    }

    /** Busca a rota apropriada à requisição fornecida */
    public function process(string $method, string $path): void
    {
        $this->notFound = true;

        foreach ($this->routes as $routeObject) {
            if ($routeObject->matchTo($method, $path) === false) {
                continue;
            }

            $this->currentRoute = $routeObject;

            $this->notFound = false;

            if ($this->accessAllowed($routeObject->policy()) === false) {
                $this->accessDenied = true;
            }

            break;
        }
    }

    private function accessAllowed(Policy|string $policy = ''): bool
    {
        if ($policy === '') {
            return true;
        }

        if (is_string($policy) === true) {
            return $this->invokePolicyString($policy);
        }

        return $policy->check();
    }

    private function invokePolicyString(string $signature): bool
    {
        if ($this->container === null) {
            throw new RuntimeException('The container is not available');
        }

        $control = new InversionOfControl($this->container);

        return $control->resolve($signature . '::check');
    }

    public function resetModuleInfo(): Router
    {
        $this->moduleIdentifier = 'all';

        return $this;
    }

    public function routeDenied(): bool
    {
        return $this->accessDenied;
    }

    public function routeNotFound(): bool
    {
        return $this->notFound;
    }

    /** @return array<int,Route> */
    public function routes(): array
    {
        return $this->routes;
    }

    public function useContainer(ContainerInterface &$container): Router
    {
        $this->container = $container;

        return $this;
    }

    private function makeRoute(string $method, string $pattern): Route
    {
        $route = new Route();
        $route->forModule($this->moduleIdentifier);
        $route->usingPattern($pattern);
        $route->usingMethod($method);

        $routeId = count($this->routes);

        $this->routes[$routeId] = $route;

        return $this->routes[$routeId];
    }
}
