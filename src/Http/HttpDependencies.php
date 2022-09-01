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
        /** @var HttpFactory $httpFactory */
        $httpFactory = $app->make(HttpFactory::class);

        $this->checkHttpFactory($httpFactory);
        
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

    /** @param HttpFactory $resource */
    private function checkHttpFactory($resource): void
    {
        if (! $resource instanceof HttpFactory) {
            throw new RuntimeException(
                "Please implement " . 
                HttpFactory::class . " dependency for " . 
                Application::class . "->bootApplication"
            );
        }
    }
}
