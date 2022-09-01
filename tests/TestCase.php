<?php

declare(strict_types=1);

namespace Tests;

use Freep\Application\Application;
use Freep\Application\Bootstrap;
use Freep\Application\Http\Request;
use Freep\Application\Http\Response;
use Freep\Application\Http\Stream;
use Freep\Application\Http\UploadedFile;
use Freep\Application\Http\Uri;
use Freep\Application\Routing\Router;
use Laminas\Diactoros\ResponseFactory;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\Diactoros\StreamFactory;
use Laminas\Diactoros\UploadedFileFactory;
use Laminas\Diactoros\UriFactory;
use PHPUnit\Framework\TestCase as FrameworkTestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\UriInterface;
use ReflectionObject;

class TestCase extends FrameworkTestCase
{
    public function getPropertyValue(object $instance, string $name)
    {
        $reflection = new ReflectionObject($instance);
        $property = $reflection->getProperty($name);
        $property->setAccessible(true);
        
        return $property->getValue($instance);
    }

    public static function applicationFactory(): Application
    {
        $app = Application::instance();

        $app->reset();
        
        $app->bootApplication(new class implements Bootstrap {
            public function bootRoutes(Router $router): void {}

            public function bootDependencies(Application $app): void {
                $app->addSingleton(Request::class, fn() => TestCase::requestFactory('/user/33'));
                $app->addSingleton(Response::class, fn() => TestCase::responseFactory());
                $app->addFactory(Stream::class, fn() => TestCase::streamFactory());
                $app->addFactory(
                    UploadedFile::class,
                    fn() => TestCase::uploadedFileFactory(TestCase::streamFactory())
                );
                $app->addFactory(Uri::class, fn() => TestCase::uriFactory());
            }
        });

        return $app;
    }

    public static function requestFactory(string $path = ''): ServerRequestInterface
    {
        $server = $_SERVER;
        $server['REQUEST_URI'] = $path;

        return ServerRequestFactory::fromGlobals($server);
    }

    public static function responseFactory(int $code = 200, string $reasonPhrase = ''): ResponseInterface
    {
        $factory = new ResponseFactory();
        return $factory->createResponse($code, $reasonPhrase);
    }

    public static function streamFactory(string $contents = ''): StreamInterface
    {
        $factory = new StreamFactory();
        return $factory->createStream($contents);
    }

    public static function uploadedFileFactory(
        StreamInterface $stream,
        ?int $size = null,
        int $error = UPLOAD_ERR_OK,
        ?string $clientFilename = null,
        ?string $clientMediaType = null
    ): UploadedFileInterface
    {
        $factory = new UploadedFileFactory();
        return $factory->createUploadedFile(
            $stream,
            $size,
            $error,
            $clientFilename,
            $clientMediaType
        );
    }

    public static function uriFactory(string $path = ''): UriInterface
    {
        $factory = new UriFactory();
        return $factory->createUri($path);
    }
}
