<?php

declare(strict_types=1);

namespace Tests\IoEngine\Console;

use Iquety\Application\IoEngine\FrontController\FcBootstrap;
use Iquety\Application\IoEngine\FrontController\Source;
use Iquety\Application\IoEngine\Action\Input;
use Iquety\Application\IoEngine\ActionDescriptor;
use Iquety\Application\IoEngine\Console\ConsoleBootstrap;
use Iquety\Application\IoEngine\Console\RoutineSource;
use Tests\IoEngine\FrontController\Stubs\Commands\OneCommand;
use Tests\IoEngine\FrontController\Stubs\Commands\SubDirectory\TwoCommand;
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
