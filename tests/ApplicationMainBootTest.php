<?php

declare(strict_types=1);

namespace Tests;

use Freep\Application\Adapter\DiactorosHttpFactory;
use Freep\Application\Application;
use Freep\Application\Bootstrap;
use Freep\Application\Http\HttpFactory;
use Freep\Application\Routing\Router;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\UriInterface;
use RuntimeException;

class ApplicationMainBootTest extends TestCase
{
    public function setUp(): void
    {
        Application::instance()->reset();
    }

    /** @test */
    public function invalidHttpFactory(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            "Please implement " . 
                HttpFactory::class . " dependency for " . 
                Application::class . "->bootApplication"
        );

        $app = Application::instance();
        $app->bootApplication(new class implements Bootstrap {
            public function bootRoutes(Router $router): void {}
            public function bootDependencies(Application $app): void {
                $app->addSingleton(HttpFactory::class, fn() => (object)[]);
            }
        });

        $app->run();
    }

    /** @test */
    public function mainDependencies(): void
    {
        $app = Application::instance();
        $app->bootApplication(new class implements Bootstrap {
            public function bootRoutes(Router $router): void {
                $router->get('/user/:id');
                $router->post('/user/:id');
            }

            public function bootDependencies(Application $app): void {
                $app->addSingleton(HttpFactory::class, DiactorosHttpFactory::class);
            }
        });

        $this->assertEquals([ 
            (new Router())->get('/user/:id'),
            (new Router())->post('/user/:id')    
        ], $app->router()->routes());

        $this->assertTrue($app->container()->has(Application::class));
        $this->assertTrue($app->container()->has(Router::class));
        $this->assertTrue($app->container()->has(ServerRequestInterface::class));
        $this->assertTrue($app->container()->has(ResponseInterface::class));
        $this->assertTrue($app->container()->has(StreamInterface::class));
        $this->assertTrue($app->container()->has(UriInterface::class));

        $this->assertFalse($app->container()->has(RequestInterface::class));
        $this->assertFalse($app->container()->has(UploadedFileInterface::class));
    }
}