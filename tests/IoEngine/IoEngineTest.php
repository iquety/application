<?php

declare(strict_types=1);

namespace Tests\IoEngine;

use Iquety\Application\IoEngine\Action\Input;
use Iquety\Application\IoEngine\Action\ActionDescriptor;
use Iquety\Application\IoEngine\IoEngine;
use Iquety\Application\IoEngine\Module;
use Iquety\Application\IoEngine\ModuleSet;
use Iquety\Application\IoEngine\SourceHandler;
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
        $engine = $this->makeIoEngine();

        $engine->useContainer(new Container());
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

        $engine = $this->makeIoEngine();

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

        $engine = $this->makeIoEngine();

        $this->assertInstanceOf(ModuleSet::class, $engine->moduleSet());
    }

    protected function makeIoEngine(): IoEngine
    {
        global $sourceHandler;

        $sourceHandler = $this->createMock(SourceHandler::class);

        return new class extends IoEngine
        {
            public function boot(Module $module): void {}
            
            public function resolve(Input $input): ?ActionDescriptor {
                return null;
            }

            public function sourceHandler(): SourceHandler
            { 
                global $sourceHandler;
                return $sourceHandler; 
            }
        };
    }
}
