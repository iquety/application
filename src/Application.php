<?php

declare(strict_types=1);

namespace Freep\Application;

use Closure;
use Freep\Application\Container\Container;
use Freep\Application\Http\Request;
use Freep\Application\Http\Response;
use Freep\Application\Http\Stream;
use Freep\Application\Http\UploadedFile;
use Freep\Application\Http\Uri;
use Freep\Application\Routing\Router;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\UriInterface;
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

    public function make(string $identifier)
    {
        return $this->container()->get($identifier);
    }

    public function bootApplication(Bootstrap $bootstrap): void
    {
        $bootstrap->bootDependencies($this);

        if (! $this->make(Request::class) instanceof ServerRequestInterface) {
            throw new RuntimeException("Please implement a " . ServerRequestInterface::class . " type request");
        }

        if (! $this->make(Response::class) instanceof ResponseInterface) {
            throw new RuntimeException("Please implement a " . ResponseInterface::class . " type response");
        }

        if (! $this->make(Stream::class) instanceof StreamInterface) {
            throw new RuntimeException("Please implement a " . StreamInterface::class . " type response");
        }

        if (! $this->make(UploadedFile::class) instanceof UploadedFileInterface) {
            throw new RuntimeException("Please implement a " . UploadedFileInterface::class . " type response");
        }

        if (! $this->make(Uri::class) instanceof UriInterface) {
            throw new RuntimeException("Please implement a " . UriInterface::class . " type response");
        }

        $this->addSingleton(Router::class, Router::class);

        $bootstrap->bootRoutes($this->router());
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

        $request = $this->make(Request::class);
        $router  = $this->router();

        $router->process(
            $request->getMethod(),
            $request->getUri()->getPath()
        );

        if ($router->routeNotFound()) {
            return $this->makeNotFoundResponse();
        }

        if ($router->routeDenied()) {
            return $this->makeAccessDeniedResponse();
        }

        try {
            $route = $router->currentRoute();
            
            $routeModule = $route->module();
            $routeCallback = $route->controller();

            if ($routeCallback === null) {
                throw new RuntimeException('The route found does not have a controller');
            }

            $this->modules[$routeModule]->bootDependencies($this);

            return call_user_func($routeCallback);
        } catch (Throwable $exception) {
            return $this->makeServerErrorResponse($exception);
        }
    }

    public function reset(): void
    {
        $this->container = new Container();
        $this->modules = [];
    }

    private function makeNotFoundResponse(): ResponseInterface
    {
        /** @var ResponseInterface $response */
        $response = $this->make(Response::class);
        $response->withStatus(404);
        $response->withBody($this->make(Stream::class, 'Not Found'));

        return $response;
    }

    private function makeAccessDeniedResponse(): ResponseInterface
    {
        $response = $this->make(Response::class);
        $response->withStatus(403);
        $response->withBody($this->make(Stream::class, 'Access denied'));

        return $response;
    }

    private function makeServerErrorResponse(Throwable $exception): ResponseInterface
    {
        $response = $this->make(Response::class);
        $response->withStatus(500);
        $response->withBody($this->make(Stream::class, $exception->getMessage()));

        return $response;
    }
}
