<?php

declare(strict_types=1);

namespace Iquety\Application\AppEngine\Mvc;

use Closure;
use Iquety\Application\Bootstrap;
use InvalidArgumentException;
use Iquety\Application\AppEngine\AppEngine;
use Iquety\Application\AppEngine\Input;
use Iquety\Application\AppEngine\MethodNotAllowedException;
use Iquety\Injection\InversionOfControl;
use Iquety\Routing\Router;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;
use Throwable;

/** @SuppressWarnings(PHPMD.CouplingBetweenObjects) */
class MvcEngine extends AppEngine
{
    private ?Router $router = null;

    public function boot(Bootstrap $bootstrap): void
    {
        if (! $bootstrap instanceof MvcBootstrap) {
            throw new InvalidArgumentException(
                sprintf('Invalid bootstrap. Required a %s', MvcBootstrap::class)
            );
        }

        $moduleIdentifier = $bootstrap::class;

        $this->router()->forModule($moduleIdentifier);

        $bootstrap->bootRoutes($this->router());
    }

    public function execute(
        RequestInterface $request,
        array $moduleList,
        Closure $bootDependencies
    ): ?ResponseInterface {
        $router = $this->router();

        if ($router->routes() === []) {
            throw new RuntimeException(
                'This bootstrap has no routes registered'
            );
        }

        $router->process($request->getMethod(), $request->getUri()->getPath());

        if ($router->routeNotFound()) {
            return null;
        }

        try {
            /** @var Route $route */
            $route = $router->currentRoute();

            $module = $route->module();
            $action = $route->action();
            $params = $route->params();

            if ($action === '') {
                throw new RuntimeException('The route found does not have a action');
            }

            $bootDependencies($moduleList[$module]);

            if ($action instanceof Closure) {
                return $this->resolveClosure($action);
            }

            $this->container()->registerSingletonDependency(
                Input::class,
                fn() => new Input($params)
            );

            $control = new InversionOfControl($this->container());

            try {
                return $control->resolveTo(Controller::class, $action, $params);
            } catch (MethodNotAllowedException) {
                return null;
            }
        } catch (Throwable $exception) {
            return $this->responseFactory()->serverErrorResponse($exception);
        }
    }

    private function resolveClosure(Closure $routeAction): ResponseInterface
    {
        $result = call_user_func($routeAction);

        $factory = $this->responseFactory();

        if ($result === null) {
            return $factory->response('');
        }

        if ($result instanceof ResponseInterface) {
            return $result;
        }

        return is_string($result)
            ? $factory->response($result)
            : $factory->jsonResponse((array)$result);
    }

    private function router(): Router
    {
        if ($this->container()->has(Router::class) === false) {
            $this->container()->registerSingletonDependency(Router::class, Router::class);
        }

        if ($this->router !== null) {
            return $this->router;
        }

        /** @var Router $router */
        $this->router = $this->container()->get(Router::class);
        $this->router->resetModuleInfo();

        return $this->router;
    }
}
