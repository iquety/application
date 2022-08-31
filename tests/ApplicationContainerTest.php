<?php

declare(strict_types=1);

namespace Tests;

use ArrayObject;
use Freep\Application\Application;
use Freep\Application\Bootstrap;
use Freep\Application\Http\Request;
use Freep\Application\Http\Response;
use Freep\Application\Routing\Router;
use RuntimeException;

class ApplicationContainerTest extends TestCase
{
    public function setUp(): void
    {
        Application::instance()->reset();
    }

    /** @test */
    public function dependencyNotExists(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            "Could not find dependency definition for " . Request::class
        );

        $app = Application::instance();
        $app->make(Request::class);
    }

    /** @test */
    public function dependencyFactory(): void
    {
        $app = Application::instance();
        $app->addFactory('identifier', ArrayObject::class);

        $this->assertNotSame($app->make('identifier'), $app->make('identifier'));
    }

    /** @test */
    public function dependencySingleton(): void
    {
        $app = Application::instance();
        $app->addSingleton('identifier', ArrayObject::class);

        $this->assertSame($app->make('identifier'), $app->make('identifier'));
    }
}
