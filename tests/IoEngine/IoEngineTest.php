<?php

declare(strict_types=1);

namespace Tests\IoEngine;

use Iquety\Application\IoEngine\ModuleSet;
use Iquety\Injection\Container;
use OutOfBoundsException;
use Tests\TestCase;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class IoEngineTest extends TestCase
{
    /** @test */
    public function gettersSuccess(): void
    {
        $engine = $this->makeGenericIoEngine();

        $engine->useContainer($this->makeContainer());
        $engine->useModuleSet(new ModuleSet());

        $this->assertInstanceOf(Container::class, $engine->container());
        $this->assertInstanceOf(ModuleSet::class, $engine->moduleSet());
    }

    /** @test */
    public function containerException(): void
    {
        $this->expectException(OutOfBoundsException::class);
        $this->expectExceptionMessage(
            'The container was not made available with the useContainer method'
        );

        $engine = $this->makeGenericIoEngine();

        $this->assertInstanceOf(Container::class, $engine->container());
        $this->assertInstanceOf(ModuleSet::class, $engine->moduleSet());
    }

    /** @test */
    public function moduleSetException(): void
    {
        $this->expectException(OutOfBoundsException::class);
        $this->expectExceptionMessage(
            'The module set was not made available with the useModuleSet method'
        );

        $engine = $this->makeGenericIoEngine();

        $this->assertInstanceOf(ModuleSet::class, $engine->moduleSet());
    }
}
