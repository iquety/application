<?php

declare(strict_types=1);

namespace Iquety\Application\Http;

use Iquety\Application\Application;
use Iquety\Injection\ContainerException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;
use ReflectionClass;
use RuntimeException;

class HttpDependencies
{
    public function attachTo(Application $app): void
    {
        $this->assertSucessfulConstruction($app, Session::class);

        $this->assertSucessfulConstruction($app, HttpFactory::class);

        /** @var HttpFactory $httpFactory */
        $httpFactory = $app->make(HttpFactory::class);

        if ($app->container()->has(ServerRequestInterface::class) === false) {
            $app->addSingleton(
                ServerRequestInterface::class,
                fn() => $httpFactory->createRequestFromGlobals()
            );
        }

        $app->addFactory(
            StreamInterface::class,
            fn(string $content = '') => $httpFactory->createStream($content)
        );

        $app->addFactory(
            UriInterface::class,
            fn(string $uri = '') => $httpFactory->createUri($uri)
        );

        $app->addFactory(
            ResponseInterface::class,
            fn(int $code = 200, string $reasonPhrase = '')
                => $httpFactory->createResponse($code, $reasonPhrase)
        );

        $serverRequest = $this->resolveDefaultRequestHeaders($app);

        $app->addSingleton(
            HttpResponseFactory::class,
            fn() => new HttpResponseFactory($httpFactory, $serverRequest)
        );
    }

    private function resolveDefaultRequestHeaders(Application $app): ServerRequestInterface
    {
        /** @var ServerRequestInterface */
        $serverRequest = $app->make(ServerRequestInterface::class);

        if ($serverRequest->getHeaderLine('Accept') === "") {
            $serverRequest = $serverRequest->withAddedHeader('Accept', HttpMime::HTML->value);
        }

        $serverRequest = $serverRequest->withAddedHeader('Environment', $app->runningMode()->value);

        return $serverRequest;
    }

    private function assertSucessfulConstruction(Application $app, string $contract): void
    {
        try {
            $instance = $app->make($contract);
        } catch (ContainerException) {
            throw new RuntimeException(sprintf(
                "Please provide an implementation for the dependency %s in the bootstrap provided in the Application->bootApplication method",
                (new ReflectionClass($contract))->getShortName(),
            ));
        }

        if (is_subclass_of($instance, $contract) === false) {
            throw new RuntimeException(sprintf(
                "The implementation provided to the %s dependency in the bootstrap provided in the Application->bootApplication method is invalid",
                (new ReflectionClass($contract))->getShortName(),
            ));
        }
    }
}
