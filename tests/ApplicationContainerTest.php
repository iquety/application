<?php

declare(strict_types=1);

namespace Tests;

use ArrayObject;
use Freep\Application\Application;
use Psr\Http\Message\ServerRequestInterface;
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
            "Could not find dependency definition for " . ServerRequestInterface::class
        );

        $app = Application::instance();
        $app->make(ServerRequestInterface::class);
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
