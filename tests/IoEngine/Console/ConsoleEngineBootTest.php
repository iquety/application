<?php

declare(strict_types=1);

namespace Tests\IoEngine\Console;

use Iquety\Application\IoEngine\Console\ConsoleEngine;
use Iquety\Application\IoEngine\Console\ConsoleModule;
use Iquety\Application\IoEngine\Console\RoutineSource;
use Iquety\Application\IoEngine\Console\RoutineSourceSet;
use Iquety\Application\IoEngine\FrontController\CommandSource;
use Iquety\Application\IoEngine\FrontController\CommandSourceSet;
use Iquety\Application\IoEngine\FrontController\FcModule;
use Iquety\Application\IoEngine\ModuleSet;
use Iquety\Application\IoEngine\Mvc\MvcModule;
use Iquety\Injection\Container;
use Iquety\Routing\Router;
use ReflectionClass;
use Tests\TestCase;

class ConsoleEngineBootTest extends TestCase
{
    /** @test */
    public function bootConsoleNotTerminal(): void
    {
        $engine = $this->createMock(ConsoleEngine::class);

        $engine->method('isTerminalExecution')
            ->willReturn(false);

        /** @var ConsoleEngine $engine */

        $engine->useContainer($this->makeContainer());

        $engine->useModuleSet(new ModuleSet());

        $engine->boot($this->makeConsoleModuleOne());

        $this->assertFalse($engine->isBooted());
    }

    /** @test */
    public function bootMvcModule(): void
    {
        $engine = $this->makeEngine($this->makeContainer());

        $engine->boot($this->makeMvcModuleOne());

        $this->assertFalse($engine->isBooted());
    }

    /** @test */
    public function bootFcModule(): void
    {
        $engine = $this->makeEngine($this->makeContainer());

        $engine->boot($this->makeFcModuleOne('Tests\Run\Actions'));

        $this->assertFalse($engine->isBooted());
    }

    /** @test */
    public function bootConsoleModule(): void
    {
        $engine = $this->makeEngine($this->makeContainer());

        $module = $this->makeConsoleModuleOne(__DIR__ . '/Console');

        $engine->boot($module);

        $this->assertTrue($engine->isBooted());

        $this->assertSame(
            'test-script',
            $engine->sourceHandler()->getScriptName()
        );

        $rootPath = dirname(__DIR__, 2);

        $this->assertSame(
            $rootPath . '/Support/Stubs',
            $engine->sourceHandler()->getScriptPath()
        );

        $this->assertCount(1, $engine->sourceHandler()->getDirectoryList());

        $this->assertSame(
            [ '/application/tests/IoEngine/Console/Console' ],
            $engine->sourceHandler()->getDirectoryList()
        );

        $this->assertTrue($engine->isBooted());
    }

    private function makeEngine(Container $container): ConsoleEngine
    {
        $engine = new ConsoleEngine();

        $engine->useContainer($container);

        $engine->useModuleSet(new ModuleSet());

        return $engine;
    }
}
