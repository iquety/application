<?php

declare(strict_types=1);

namespace Tests;

use Freep\Application\Application;
use Freep\Application\Bootstrap;
use Freep\Application\Http\Request;
use Freep\Application\Http\Response;
use Freep\Application\Routing\Router;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;

class ApplicationMainBootTest extends TestCase
{
    public function setUp(): void
    {
        Application::instance()->reset();
    }

    /** @test */
    public function invalidRequest(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            "Please implement a " . ServerRequestInterface::class . " type request"
        );

        $app = Application::instance();
        $app->bootApplication(new class implements Bootstrap {
            public function bootRoutes(Router $router): void {}
            public function bootDependencies(Application $app): void {
                $app->addSingleton(Request::class, fn() => (object)[]);
            }
        });

        $app->run();
    }

    /** @test */
    public function invalidResponse(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            "Please implement a " . ResponseInterface::class . " type response"
        );

        $app = Application::instance();
        $app->bootApplication(new class implements Bootstrap {
            public function bootRoutes(Router $router): void {}
            public function bootDependencies(Application $app): void {
                $app->addSingleton(Request::class, fn() => TestCase::requestFactory());
                $app->addSingleton(Response::class, fn() => (object)[]);
            }
        });
    }

    /** @test */
    public function bootApplication(): void
    {
        $app = Application::instance();
        $app->bootApplication(new class implements Bootstrap {
            public function bootRoutes(Router $router): void {
                $router->get('/user/:id');
                $router->post('/user/:id');
            }

            public function bootDependencies(Application $app): void {
                $app->addSingleton(Request::class, fn() => TestCase::requestFactory());
                $app->addSingleton(Response::class, fn() => TestCase::responseFactory());
            }
        });

        $this->assertEquals([ 
            (new Router())->get('/user/:id'),
            (new Router())->post('/user/:id')    
        ], $app->router()->routes());

        $this->assertTrue($app->container()->has(Request::class));
        $this->assertTrue($app->container()->has(Response::class));
    }
}