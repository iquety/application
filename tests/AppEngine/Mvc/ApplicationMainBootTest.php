<?php

declare(strict_types=1);

namespace Tests\AppEngine\Mvc;

use Iquety\Application\Adapter\HttpFactory\DiactorosHttpFactory;
use Iquety\Application\Adapter\Session\MemorySession;
use Iquety\Application\Application;
use Iquety\Application\Http\HttpFactory;
use Iquety\Application\Http\Session;
use Iquety\Application\AppEngine\Mvc\MvcBootstrap;
use Iquety\Application\AppEngine\Mvc\MvcEngine;
use Iquety\Routing\Router;
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
        $app = TestCase::applicationFactory('user/4');

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

        $this->assertCount(2, $app->make(Router::class)->routes());

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
