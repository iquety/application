<?php

declare(strict_types=1);

namespace Tests;

use ArrayObject;
use Freep\Application\Application;
use Freep\Application\Bootstrap;
use Freep\Application\Http\Request;
use Freep\Application\Http\Response;
use Freep\Application\Routing\Router;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

class ApplicationTest extends TestCase
{
    public function setUp(): void
    {
        Application::instance()->reset();
    }

    /** @test */
    public function sigleton(): void
    {
        $this->assertNotSame(new ArrayObject(), new ArrayObject());
        $this->assertSame(Application::instance(), Application::instance());
    }

    /** @test */
    public function runApplication(): void
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

        $app->run();
    }
}
