<?php

declare(strict_types=1);

namespace Tests;

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

    /**
     * @test
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function invalidSession(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            "Please implement " .
                Session::class . " dependency for " .
                Application::class . "->bootApplication"
        );

        $app = Application::instance();

        $app->bootApplication(new class implements Bootstrap {
            public function bootDependencies(Application $app): void
            {
                $app->addSingleton(Session::class, fn() => (object)[]);
                $app->addSingleton(HttpFactory::class, fn() => (object)[]);
            }
        });

        $app->run();
    }

    /**
     * @test
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
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
            public function bootDependencies(Application $app): void
            {
                $app->addSingleton(Session::class, MemorySession::class);
                $app->addSingleton(HttpFactory::class, fn() => (object)[]);
            }
        });

        $app->run();
    }
}
