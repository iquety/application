<?php

declare(strict_types=1);

namespace Tests\AppEngine\Console;

use Iquety\Application\AppEngine\FrontController\FcBootstrap;
use Iquety\Application\AppEngine\FrontController\Source;
use Iquety\Application\AppEngine\Action\Input;
use Iquety\Application\AppEngine\ActionDescriptor;
use Iquety\Application\AppEngine\Console\ConsoleBootstrap;
use Iquety\Application\AppEngine\Console\RoutineSource;
use Tests\AppEngine\FrontController\Stubs\Commands\OneCommand;
use Tests\AppEngine\FrontController\Stubs\Commands\SubDirectory\TwoCommand;
use Tests\TestCase;

/** @SuppressWarnings(PHPMD.StaticAccess) */
class RoutineSourceTest extends TestCase
{
    /** @test */
    public function objectConstruction(): void
    {
        new RoutineSource(__DIR__);

        $this->assertTrue(true);
    }

    /** @test */
    public function getIdentity(): void
    {
        $directory = new RoutineSource(__DIR__);

        $this->assertSame(
            md5(__DIR__),
            $directory->getIdentity()
        );
    }

    /** @test */
    public function getDescriptorLevelOne(): void
    {
        $directory = new RoutineSource(__DIR__);

        $descriptor = $directory->getDescriptorTo(
            ConsoleBootstrap::class,
            Input::fromConsoleArguments(['one-command'])
        );

        $this->assertInstanceOf(ActionDescriptor::class, $descriptor);
        $this->assertSame("::", $descriptor->action());
    }
}
