<?php

declare(strict_types=1);

namespace Iquety\Application\Layers\Mvc;

use Closure;
use Iquety\Application\Bootstrap;
use Iquety\Application\Engine;
use Iquety\Application\Http\HttpResponseFactory;
use Iquety\Application\Routing\Router;
use InvalidArgumentException;
use Iquety\Injection\InversionOfControl;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;
use Throwable;

class MvcEngine extends Engine
{
    private ?Router $router = null;

    public function boot(string $moduleIdentifier, Bootstrap $bootstrap): void
    {
        if (! $bootstrap instanceof MvcBootstrap) {
            throw new InvalidArgumentException(
                sprintf('Invalid bootstrap. Required a %s', MvcBootstrap::class)
            );
        }

        $this->router()->forModule($moduleIdentifier);
        
        $bootstrap->bootRoutes($this->router());
    }

    public function execute(array $moduleList, RequestInterface $request): ?ResponseInterface
    {
        $this->router()->process($request->getMethod(), $request->getUri()->getPath());

        if ($this->router()->routeNotFound()) {
            return null;
        }

        if ($this->router()->routeDenied()) {
            return (new HttpResponseFactory($this->app()))->accessDeniedResponse();
        }

        try {
            /** @var Route $route */
            $route = $this->router()->currentRoute();

            $routeModule = $route->module();
            $routeAction = $route->action();

            if ($routeAction === '') {
                throw new RuntimeException('The route found does not have a action');
            }

            $moduleList[$routeModule]->bootDependencies($this->app());

            if ($routeAction instanceof Closure) {
                return $this->resolveClosure($routeAction);
            }

            $control = new InversionOfControl($this->app()->container());

            return $control->resolve($routeAction, $route->params());
        } catch (Throwable $exception) {
            return (new HttpResponseFactory($this->app()))->serverErrorResponse($exception);
        }
    }

    private function resolveClosure(Closure $routeAction): ResponseInterface
    {
        $result = call_user_func($routeAction);

        $factory = new HttpResponseFactory($this->app());

        if ($result === null) {
            return $factory->response('');
        }

        return is_string($result)
            ? $factory->response($result)
            : $factory->jsonResponse($result);
    }

    private function router(): Router
    {
        if ($this->app()->container()->has(Router::class) === false) {
            $this->app()->addSingleton(Router::class, Router::class);
        }

        if ($this->router !== null) {
            return $this->router;
        }

        $container = $this->app()->container();

        /** @var Router $router */
        $this->router = $this->app()->make(Router::class);
        $this->router->resetModuleInfo();
        $this->router->useContainer($container);

        return $this->router;
    }
}

