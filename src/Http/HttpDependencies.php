<?php

declare(strict_types=1);

namespace Iquety\Application\Http;

use Iquety\Application\Application;
use Iquety\Injection\ContainerException;
use Iquety\Injection\NotFoundException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;
use ReflectionClass;
use RuntimeException;

use function PHPUnit\Framework\throwException;

class HttpDependencies
{
    public function attachTo(Application $app): void
    {
        $this->assertSucessfulConstruction($app, Session::class);

        /** @var HttpFactory $httpFactory */
        $httpFactory = $this->assertSucessfulConstruction($app, HttpFactory::class);

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

        $app->addSingleton(
            HttpResponseFactory::class,
            fn() => new HttpResponseFactory($httpFactory)
        );
    }

    private function assertSucessfulConstruction(Application $app, string $contract): mixed
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

        return $instance;
    }
}
