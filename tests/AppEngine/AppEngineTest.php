<?php

declare(strict_types=1);

namespace Tests\AppEngine;

use Iquety\Application\AppEngine\Action\Input;
use Iquety\Application\AppEngine\ActionDescriptor;
use Iquety\Application\AppEngine\AppEngine;
use Iquety\Application\AppEngine\Bootstrap;
use Iquety\Application\AppEngine\FrontController\FcSourceHandler;
use Iquety\Application\AppEngine\ModuleSet;
use Iquety\Application\AppEngine\SourceHandler;
use Iquety\Injection\Container;
use OutOfBoundsException;
use Tests\TestCase;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class AppEngineTest extends TestCase
{
    /** @test */
    public function gettersSuccess(): void
    {
        $engine = $this->makeAppEngine();

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

        $engine = $this->makeAppEngine();

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

        $engine = $this->makeAppEngine();

        $this->assertInstanceOf(ModuleSet::class, $engine->moduleSet());
    }

    protected function makeAppEngine(): AppEngine
    {
        global $sourceHandler;

        $sourceHandler = $this->createMock(SourceHandler::class);

        return new class extends AppEngine
        {
            public function boot(Bootstrap $bootstrap): void {}
            
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
