<?php

declare(strict_types=1);

namespace Freep\Application;

use Closure;
use Freep\Application\Container\Container;
use Freep\Application\Container\InversionOfControl;
use Freep\Application\Http\HttpDependencies;
use Freep\Application\Http\HttpResponseFactory;
use Freep\Application\Routing\Router;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;
use Throwable;

class Application
{
    private static ?Application $instance = null;

    private Container $container;

    private array $modules = [];

    public static function instance(): Application
    {
        if (static::$instance === null) {
            static::$instance = new self();
        }

        return static::$instance;
    }

    private function __construct()
    {
        $this->container = new Container();
    }

    public function addFactory(string $identifier, Closure|string $factory): void
    {
        $this->container()->registerDependency($identifier, $factory);
    }

    public function addSingleton(string $identifier, Closure|string $factory): void
    {
        $this->container()->registerSingletonDependency($identifier, $factory);
    }

    public function make(...$arguments)
    {   
        $identifier = array_shift($arguments);

        return $this->container()->getWithArguments($identifier, $arguments);
    }

    public function bootApplication(Bootstrap $bootstrap): void
    {
        $bootstrap->bootDependencies($this);

        $this->setupMainDependencies();

        $bootstrap->bootRoutes($this->router());
    }

    private function setupMainDependencies(): void
    {
        (new HttpDependencies())->attachTo($this);

        // para o ioc fazer uso da aplicação
        $this->addSingleton(Application::class, fn() => Application::instance());

        $this->addSingleton(Router::class, Router::class);
    }

    public function bootModule(Bootstrap $bootstrap): void
    {
        $this->modules[$bootstrap::class] = $bootstrap;
    }

    public function container(): Container
    {
        return $this->container;
    }

    public function router(): Router
    {
        return $this->make(Router::class)->resetModuleInfo();
    }

    public function run(): ResponseInterface
    {
        foreach ($this->modules as $identifier => $bootstrap) {
            $bootstrap->bootRoutes($this->router()->forModule($identifier));
        }

        $request = $this->make(ServerRequestInterface::class);
        $router  = $this->router();

        $router->process(
            $request->getMethod(),
            $request->getUri()->getPath()
        );

        if ($router->routeNotFound()) {
            return (new HttpResponseFactory($this))->notFoundResponse();
        }

        if ($router->routeDenied()) {
            return (new HttpResponseFactory($this))->accessDeniedResponse();
        }

        try {
            $route = $router->currentRoute();
            
            $routeModule = $route->module();
            $routeAction = $route->action();

            if ($routeAction === null) {
                throw new RuntimeException('The route found does not have a action');
            }

            $this->modules[$routeModule]->bootDependencies($this);

            if ($routeAction instanceof Closure) {
                return call_user_func($routeAction);
            }

            $control = new InversionOfControl($this->container());
            
            return $control->resolve($routeAction, $route->params());
            
        } catch (Throwable $exception) {
            return (new HttpResponseFactory($this))->serverErrorResponse($exception);
        }
    }

    public function reset(): void
    {
        $this->container = new Container();
        $this->modules = [];
    }
}
