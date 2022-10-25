<?php

declare(strict_types=1);

namespace Tests\Layers\Mvc;

use Freep\Application\Adapter\HttpFactory\DiactorosHttpFactory;
use Freep\Application\Adapter\Session\MemorySession;
use Freep\Application\Application;
use Freep\Application\Bootstrap;
use Freep\Application\Http\HttpFactory;
use Freep\Application\Http\Session;
use Freep\Application\Layers\Mvc\MvcBootstrap;
use Freep\Application\Layers\Mvc\MvcEngine;
use Freep\Application\Routing\Router;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\UriInterface;
use RuntimeException;
use Tests\TestCase;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ApplicationMainBootTest extends TestCase
{
    public function setUp(): void
    {
        Application::instance()->reset();
    }

    /** @test */
    public function routerDependency(): void
    {
        $app = Application::instance();

        $app->addEngine(MvcEngine::class);
        
        $app->bootApplication(new class extends MvcBootstrap {
            public function bootRoutes(Router $router): void
            {
                $router->get('/user/:id');
                $router->post('/user/:id');
            }

            public function bootDependencies(Application $app): void
            {
                $app->addSingleton(Session::class, MemorySession::class);
                $app->addSingleton(HttpFactory::class, DiactorosHttpFactory::class);
            }
        });

        $app->run();

        $this->assertEquals([
            (new Router())->get('/user/:id'),
            (new Router())->post('/user/:id')
        ], $app->make(Router::class)->routes());

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
