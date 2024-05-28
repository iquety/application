<?php

declare(strict_types=1);

namespace Iquety\Application\Http;

use Iquety\Application\Environment;
use Iquety\Injection\Container;
use Iquety\Injection\ContainerException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;
use ReflectionClass;
use RuntimeException;

/** @SuppressWarnings(PHPMD.CouplingBetweenObjects) */
class HttpDependencies
{
    public function __construct(private Environment $environment)
    {
    }

    public function attachTo(Container $container): void
    {
        $this->assertSucessfulConstruction($container, Session::class);

        $this->assertSucessfulConstruction($container, HttpFactory::class);

        /** @var HttpFactory $httpFactory */
        $httpFactory = $container->get(HttpFactory::class);

        if ($container->has(ServerRequestInterface::class) === false) {
            $container->addSingleton(
                ServerRequestInterface::class,
                fn() => $httpFactory->createRequestFromGlobals()
            );
        }

        $container->addFactory(
            StreamInterface::class,
            fn(string $content = '') => $httpFactory->createStream($content)
        );

        $container->addFactory(
            UriInterface::class,
            fn(string $uri = '') => $httpFactory->createUri($uri)
        );

        $container->addFactory(
            ResponseInterface::class,
            fn(int $code = 200, string $reasonPhrase = '')
                => $httpFactory->createResponse($code, $reasonPhrase)
        );

        $serverRequest = $this->resolveDefaultRequestHeaders($container);

        $container->addSingleton(
            HttpResponseFactory::class,
            fn() => new HttpResponseFactory($httpFactory, $serverRequest)
        );
    }

    private function resolveDefaultRequestHeaders(Container $container): ServerRequestInterface
    {
        /** @var ServerRequestInterface */
        $serverRequest = $container->get(ServerRequestInterface::class);

        if ($serverRequest->getHeaderLine('Accept') === "") {
            $serverRequest = $serverRequest->withAddedHeader('Accept', HttpMime::HTML->value);
        }

        $serverRequest = $serverRequest->withAddedHeader(
            'Environment',
            $this->environment->value
        );

        return $serverRequest;
    }

    /** @param class-string $contract */
    private function assertSucessfulConstruction(Container $container, string $contract): void
    {
        try {
            $instance = $container->get($contract);
        } catch (ContainerException) {
            throw new RuntimeException(sprintf(
                'Please provide an implementation for the dependency %s in the bootstrap ' .
                'provided in the Application->bootApplication method',
                (new ReflectionClass($contract))->getShortName(),
            ));
        }

        if (is_subclass_of($instance, $contract) === false) {
            throw new RuntimeException(sprintf(
                'The implementation provided to the %s dependency in the bootstrap ' .
                'provided in the Application->bootApplication method is invalid',
                (new ReflectionClass($contract))->getShortName(),
            ));
        }
    }
}
