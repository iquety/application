<?php

declare(strict_types=1);

namespace Freep\Application\Http;

use Freep\Application\Application;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;
use RuntimeException;

class HttpDependencies
{
    public function attachTo(Application $app): void
    {
        $this->assertImplementation($app->make(Session::class), Session::class);

        /** @var HttpFactory $httpFactory */
        $httpFactory = $app->make(HttpFactory::class);

        $this->assertImplementation($httpFactory, HttpFactory::class);

        $app->addSingleton(
            ServerRequestInterface::class,
            fn() => $httpFactory->createRequestFromGlobals()
        );

        $app->addFactory(
            ResponseInterface::class,
            fn(
                int $code = 200,
                string $reasonPhrase = ''
            ) => $httpFactory->createResponse($code, $reasonPhrase)
        );

        $app->addFactory(
            StreamInterface::class,
            fn(string $content = '') => $httpFactory->createStream($content)
        );

        $app->addFactory(
            UriInterface::class,
            fn(string $uri = '') => $httpFactory->createUri($uri)
        );
    }

    private function assertImplementation(mixed $resource, string $requiredType): void
    {
        if (! is_subclass_of($resource, $requiredType)) {
            throw new RuntimeException(
                "Please implement $requiredType dependency for " .
                Application::class . "->bootApplication"
            );
        }
    }
}
