<?php

declare(strict_types=1);

namespace Iquety\Application\Layers\FrontController;

use Iquety\Application\Bootstrap;
use Iquety\Application\Engine;
use InvalidArgumentException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class FrontControllerEngine extends Engine
{
    public function boot(string $moduleIdentifier, Bootstrap $bootstrap): void
    {
        if (! $bootstrap instanceof FrontControllerBootstrap) {
            throw new InvalidArgumentException(
                sprintf('Invalid bootstrap. Required a %s', FrontControllerBootstrap::class)
            );
        }

        // verifica se o comando está presente
        
        
    }

    public function execute(array $moduleList, RequestInterface $request): ?ResponseInterface
    {
        return null;
        
        // $this->router()->process($request->getMethod(), $request->getUri()->getPath());

        // if ($this->router()->routeNotFound()) {
        //     return null;
        // }

        // if ($this->router()->routeDenied()) {
        //     return (new HttpResponseFactory($this->app()))->accessDeniedResponse();
        // }

        // try {
        //     /** @var Route $route */
        //     $route = $this->router()->currentRoute();

        //     $routeModule = $route->module();
        //     $routeAction = $route->action();

        //     if ($routeAction === '') {
        //         throw new RuntimeException('The route found does not have a action');
        //     }

        //     $moduleList[$routeModule]->bootDependencies($this->app());

        //     if ($routeAction instanceof Closure) {
        //         return $this->resolveClosure($routeAction);
        //     }

        //     $control = new InversionOfControl($this->app()->container());

        //     return $control->resolve($routeAction, $route->params());
        // } catch (Throwable $exception) {
        //     return (new HttpResponseFactory($this->app()))->serverErrorResponse($exception);
        // }
    }
}

