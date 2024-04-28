<?php

declare(strict_types=1);

namespace Tests\Unit\Application;

use ArrayObject;
use Iquety\Application\Application;
use InvalidArgumentException;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;
use Tests\Unit\TestCase;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
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
    public function dependencyInvalidCall(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Dependency id was not specified');

        $app = Application::instance();
        $app->make();
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

    /** @test */
    public function dependencyFactoryWithArguments(): void
    {
        $app = Application::instance();
        $app->addFactory('identifier', fn($teste) => new ArrayObject([ 'teste' => $teste ]));

        $this->assertNotSame(
            $app->make('identifier', 'none', 'naitis'),
            $app->make('identifier', 'ho')
        );
    }
}
