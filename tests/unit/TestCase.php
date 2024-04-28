<?php

declare(strict_types=1);

namespace Tests\Unit;

use Closure;
use Iquety\Application\Adapter\HttpFactory\DiactorosHttpFactory;
use Iquety\Application\Adapter\HttpFactory\GuzzleHttpFactory;
use Iquety\Application\Adapter\HttpFactory\NyHolmHttpFactory;
use Iquety\Application\AppEngine\AppEngine;
use Iquety\Application\Application;
use Iquety\Application\Bootstrap;
use Iquety\Application\Http\HttpFactory;
use Iquety\Application\Http\HttpMethod;
use Iquety\Application\Http\HttpResponseFactory;
use Iquety\Injection\Container;
use OutOfBoundsException;
use PHPUnit\Framework\TestCase as FrameworkTestCase;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionClass;
use ReflectionObject;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
abstract class TestCase extends FrameworkTestCase
{
    private ?Container $appContainer = null;

    // factories

    protected function requestFactory(
        HttpFactory $httpFactory,
        string $path = '',
        string $method = HttpMethod::ANY
    ): ServerRequestInterface {
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
        /** @var HttpFactory */
        return new $httpFactoryContract();
    }

    protected function httpResponseFactory(HttpFactory $httpFactory): HttpResponseFactory
    {
        return new HttpResponseFactory($httpFactory);
    }

    /** @param class-string<AppEngine> $engineContract */
    protected function appEngineFactory(HttpFactory $httpFactory, string $engineContract): AppEngine
    {
        $this->appContainer = new Container();

        $this->appContainer->registerSingletonDependency(
            HttpResponseFactory::class,
            fn() => $this->httpResponseFactory($httpFactory)
        );

        // FcEngine | MvcEngine
        $engine = new $engineContract();

        $engine->useContainer($this->appContainer);

        return $engine;
    }

    protected function appEngineContainer(): Container
    {
        if ($this->appContainer === null) {
            throw new OutOfBoundsException(
                'The container will only be available after invoking the appEngineFactory method'
            );
        }

        return $this->appContainer;
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @SuppressWarnings(PHPMD.Superglobals)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @SuppressWarnings(PHPMD.UndefinedVariable)
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
                $routine = $this->setup ?? fn() => null;
                $routine($app);
            }
        };

        $bootstrap->setupDependencies($dependencies ?? fn() => null);

        return $bootstrap;
    }

    // tools

    /** @param class-string<object> $signature */
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

    /** @return array<string,array<class-string>> */
    public function httpFactoryProvider(): array
    {
        return [
            'Diactoros' => [ DiactorosHttpFactory::class ],
            'Guzzle'    => [ GuzzleHttpFactory::class ],
            'NyHolm'    => [ NyHolmHttpFactory::class ],
        ];
    }
}
