<?php

declare(strict_types=1);

namespace Tests\Support\Stubs;

use Iquety\Application\IoEngine\Mvc\MvcModule;
use Iquety\Http\HttpMethod;
use Iquety\Injection\Container;
use Iquety\Routing\Router;

class MvcModuleOne extends MvcModule
{
    /** @param array<string,mixed> $dependencyList */
    public function __construct(
        private HttpMethod $method = HttpMethod::ANY,
        private string $routePath = '/',
        private string $routeAction = '',
        private array $dependencyList = []
    ) {
    }

    public function bootDependencies(Container $container): void
    {
        foreach ($this->dependencyList as $signature => $dependency) {
            $container->addFactory($signature, $dependency);
        }
    }

    public function bootRoutes(Router &$router): void
    {
        $method = match ($this->method) {
            HttpMethod::ANY => 'any',
            HttpMethod::GET => 'get',
            HttpMethod::POST => 'post',
            HttpMethod::PUT => 'put',
            HttpMethod::PATCH => 'patch',
            HttpMethod::DELETE => 'delete',
        };

        if ($this->routeAction === '') {
            $router->{$method}($this->routePath);

            return;
        }

        $parts = array_filter(explode('@', $this->routeAction));

        $className = $parts[0];
        $operationName = $parts[1] ?? '';

        $router->{$method}($this->routePath)->usingAction($className, $operationName);
    }
}
