<?php

declare(strict_types=1);

namespace Tests\AppEngine\Console;

use InvalidArgumentException;
use Iquety\Application\AppEngine\Action\Input;
use Iquety\Application\AppEngine\ActionDescriptor;
use Iquety\Application\AppEngine\Console\ConsoleBootstrap;
use Iquety\Application\AppEngine\Console\RoutineSource;
use Iquety\Application\AppEngine\Console\RoutineSourceSet;
use Iquety\Application\AppEngine\Console\Script;
use Tests\TestCase;

/** @SuppressWarnings(PHPMD.StaticAccess) */
class RoutineSourceSetTest extends TestCase
{
    /** @test */
    public function addDirectory(): void
    {
        $directorySet = new RoutineSourceSet(ConsoleBootstrap::class);

        $directorySet->add(new RoutineSource(__DIR__));

        $this->assertCount(1, $directorySet->toArray());
    }

    /** @test */
    public function addSameDirectory(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The specified source already exists');

        $directorySet = new RoutineSourceSet(ConsoleBootstrap::class);

        $directorySet->add(new RoutineSource(__DIR__));

        $directorySet->add(new RoutineSource(__DIR__));
    }

    /** @test */
    public function addTwoDirectories(): void
    {
        $directorySet = new RoutineSourceSet(ConsoleBootstrap::class);

        $directorySet->add(new RoutineSource(__DIR__));

        $directorySet->add(new RoutineSource(__DIR__ . '/Stubs'));

        $this->assertCount(2, $directorySet->toArray());
    }

    /** @test */
    public function hasSources(): void
    {
        $directorySet = new RoutineSourceSet(ConsoleBootstrap::class);

        $this->assertFalse($directorySet->hasSources());

        $directorySet->add(new RoutineSource(__DIR__));

        $this->assertTrue($directorySet->hasSources());
    }

    /** @test */
    public function getDescriptorWithoutSources(): void
    {
        $directorySet = new RoutineSourceSet(ConsoleBootstrap::class);

        $this->assertNull($directorySet->getDescriptorTo(
            Input::fromConsoleArguments(['one-command'])
        ));
    }

    /** @test */
    public function getDescriptor(): void
    {
        $directorySet = new RoutineSourceSet(ConsoleBootstrap::class);

        $directorySet->add(new RoutineSource(__DIR__ . '/Stubs'));

        $descriptor = $directorySet->getDescriptorTo(
            Input::fromConsoleArguments(['one-routine'])
        );

        $this->assertInstanceOf(ActionDescriptor::class, $descriptor);
        $this->assertSame(Script::class, $descriptor->type());
        $this->assertSame(ConsoleBootstrap::class, $descriptor->module());
        $this->assertSame('::', $descriptor->action());
    }

    /** @test */
    public function getInexistent(): void
    {
        $directorySet = new RoutineSourceSet(ConsoleBootstrap::class);

        $directorySet->add(new RoutineSource(__DIR__ . '/Stubs'));

        $descriptor = $directorySet->getDescriptorTo(
            Input::fromConsoleArguments(['not-exists'])
        );

        $this->assertInstanceOf(ActionDescriptor::class, $descriptor);
        $this->assertSame(Script::class, $descriptor->type());
        $this->assertSame(ConsoleBootstrap::class, $descriptor->module());
        $this->assertSame('::', $descriptor->action());
    }
}
