<?php

declare(strict_types=1);

namespace Tests;

use Freep\Application\Application;
use Freep\Application\Bootstrap;
use Freep\Application\Http\Request;
use Freep\Application\Http\Response;
use Freep\Application\Routing\Router;
use Laminas\Diactoros\ResponseFactory;
use Laminas\Diactoros\ServerRequestFactory;
use PHPUnit\Framework\TestCase as FrameworkTestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
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
                $app->addSingleton(Request::class, fn() => TestCase::requestFactory());
                $app->addSingleton(Response::class, fn() => TestCase::responseFactory());
            }
        });

        return $app;
    }

    public static function requestFactory(): ServerRequestInterface
    {
        return ServerRequestFactory::fromGlobals();
    }

    public static function responseFactory(int $code = 200, string $reasonPhrase = ''): ResponseInterface
    {
        $factory = new ResponseFactory();
        return $factory->createResponse($code, $reasonPhrase);
    }
}
