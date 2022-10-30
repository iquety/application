<?php

declare(strict_types=1);

namespace Tests;

use Iquety\Application\Adapter\HttpFactory\DiactorosHttpFactory;
use Iquety\Application\Adapter\Session\MemorySession;
use Iquety\Application\Application;
use Iquety\Application\Bootstrap;
use Iquety\Application\Http\HttpFactory;
use Iquety\Application\Http\Session;
use Iquety\Routing\Router;
use Laminas\Diactoros\ServerRequestFactory;
use PHPUnit\Framework\TestCase as FrameworkTestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\UriInterface;
use ReflectionObject;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
class TestCase extends FrameworkTestCase
{
    public function getPropertyValue(object $instance, string $name): mixed
    {
        $reflection = new ReflectionObject($instance);
        $property = $reflection->getProperty($name);
        $property->setAccessible(true);

        return $property->getValue($instance);
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @SuppressWarnings(PHPMD.Superglobals)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public static function applicationFactory(): Application
    {
        $app = Application::instance();

        $app->reset();
        $app->disableHeadersEmission();

        $app->bootApplication(new class implements Bootstrap {
            public function bootRoutes(Router $router): void
            {
            }

            public function bootDependencies(Application $app): void
            {
                $app->addSingleton(Session::class, MemorySession::class);

                $app->addSingleton(HttpFactory::class, function () {
                    return new class extends DiactorosHttpFactory {
                        public function createRequestFromGlobals(): ServerRequestInterface
                        {
                            $server = $_SERVER;
                            $server['REQUEST_URI'] = '/user/33';

                            return (new ServerRequestFactory())->fromGlobals($server);
                        }
                    };
                });
            }
        });

        return $app;
    }

    /**
     * @SuppressWarnings(PHPMD)
     */
    public static function requestFactory(string $path = ''): ServerRequestInterface
    {
        $factory = new class extends DiactorosHttpFactory {
            public string $path = '';

            public function createRequestFromGlobals(): ServerRequestInterface
            {
                $server = $_SERVER;
                $server['REQUEST_URI'] = $this->path;

                return (new ServerRequestFactory())->fromGlobals($server);
            }
        };

        $factory->path = $path;

        return $factory->createRequestFromGlobals();
    }

    public static function responseFactory(int $code = 200, string $reasonPhrase = ''): ResponseInterface
    {
        return (new DiactorosHttpFactory())->createResponse($code, $reasonPhrase);
    }

    public static function streamFactory(string $content = ''): StreamInterface
    {
        return (new DiactorosHttpFactory())->createStream($content);
    }

    public static function uploadedFileFactory(
        StreamInterface $stream,
        ?int $size = null,
        int $error = UPLOAD_ERR_OK,
        ?string $clientFilename = null,
        ?string $clientMediaType = null
    ): UploadedFileInterface {
        return (new DiactorosHttpFactory())->createUploadedFile(
            $stream,
            $size,
            $error,
            $clientFilename,
            $clientMediaType
        );
    }

    public static function uriFactory(string $path = ''): UriInterface
    {
        return (new DiactorosHttpFactory())->createUri($path);
    }
}
