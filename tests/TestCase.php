<?php

declare(strict_types=1);

namespace Tests;

use Closure;
use Iquety\Application\Adapter\HttpFactory\DiactorosHttpFactory;
use Iquety\Application\Adapter\HttpFactory\GuzzleHttpFactory;
use Iquety\Application\Adapter\HttpFactory\NyHolmHttpFactory;
use Iquety\Application\Adapter\Session\MemorySession;
use Iquety\Application\AppEngine\AppEngine;
use Iquety\Application\Application;
use Iquety\Application\Bootstrap;
use Iquety\Application\Http\HttpFactory;
use Iquety\Application\Http\HttpMethod;
use Iquety\Application\Http\HttpResponseFactory;
use Iquety\Application\Http\Session;
use Iquety\Injection\Container;
use Iquety\Routing\Router;
use Laminas\Diactoros\ServerRequestFactory;
use PHPUnit\Framework\MockObject\Builder\InvocationMocker;
use PHPUnit\Framework\TestCase as FrameworkTestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\UriInterface;
use ReflectionClass;
use ReflectionObject;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
abstract class TestCase extends FrameworkTestCase
{
    private static ?Container $container = null;

    // factories

    protected function requestFactory(
        HttpFactory $httpFactory,
        string $path = '',
        string $method = HttpMethod::ANY
    ): ServerRequestInterface
    {
        $request = $httpFactory->createRequestFromGlobals();

        if ($path === '') {
            return $request;
        }

        if ($method !== HttpMethod::ANY) {
            $request = $request->withMethod($method);
        }

        return $request->withUri(
            $httpFactory->createUri("http://localhost/" . trim($path, '/'))
        );
    }

    protected function httpFactory(string $httpFactoryContract): HttpFactory
    {
        return new $httpFactoryContract();
    }

    protected function httpResponseFactory(HttpFactory $httpFactory): HttpResponseFactory
    {
        return new HttpResponseFactory($httpFactory);
    }

    protected function appEngineFactory(HttpFactory $httpFactory, string $engineContract): AppEngine
    {
        static::$container = new Container();
        static::$container->registerSingletonDependency(
            HttpResponseFactory::class,
            fn() => $this->httpResponseFactory($httpFactory)
        );

        // FcEngine | MvcEngine
        $engine = new $engineContract();

        $engine->useContainer(static::$container);

        return $engine;
    }

    protected function appEngineContainer(): Container
    {
        return static::$container;
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @SuppressWarnings(PHPMD.Superglobals)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public static function appBootstrapFactory(?Closure $dependencies = null): Bootstrap
    {
        $bootstrap = new class implements Bootstrap
        {
            private ?Closure $setup = null;

            public function setupDependencies(Closure $routine): void
            {
                $this->setup = $routine;
            }

            public function bootDependencies(Application $app): void
            {
                $routine = $this->setup;
                $routine($app);
            }
        };

        $bootstrap->setupDependencies($dependencies ?? function(){});

        return $bootstrap;
    }

    // tools

    protected function extractNamespace(string $signature, string $addNode = ''): string
    {
        $namespace = (new ReflectionClass($signature))->getNamespaceName();
        
        if ($addNode !== '') {
            $namespace .= "\\$addNode";
        }

        return $namespace;
    }

    protected function getPropertyValue(object $instance, string $name): mixed
    {
        $reflection = new ReflectionObject($instance);
        $property = $reflection->getProperty($name);
        $property->setAccessible(true);

        return $property->getValue($instance);
    }

    // - - - - - - - - - - - - - - - - - - - -
    // data providers

    public function httpFactoryProvider(): array
    {
        return [
            'Diactoros' => [ DiactorosHttpFactory::class ],
            'Guzzle'    => [ GuzzleHttpFactory::class ],
            'NyHolm'    => [ NyHolmHttpFactory::class ],
        ];
    }
}
