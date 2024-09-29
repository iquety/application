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

        $engine->useContainer(new Container());

        $engine->useModuleSet(new ModuleSet());

        $engine->boot($this->makeConsoleModule());

        $this->assertFalse($engine->isBooted());
    }

    /** @test */
    public function bootMvcModule(): void
    {
        $engine = $this->makeEngine(new Container());

        $engine->boot($this->makeMvcModule());

        $this->assertFalse($engine->isBooted());
    }

    /** @test */
    public function bootFcModule(): void
    {
        $engine = $this->makeEngine(new Container());

        $engine->boot($this->makeFcModule());

        $this->assertFalse($engine->isBooted());
    }

    /** @test */
    public function bootConsoleModule(): void
    {
        $engine = $this->makeEngine(new Container());

        $engine->boot($this->makeConsoleModule());

        $this->assertTrue($engine->isBooted());

        $this->assertSame(
            'test-script',
            $engine->sourceHandler()->getCommandName()
        );

        $this->assertSame(
            __DIR__,
            $engine->sourceHandler()->getCommandPath()
        );

        $this->assertCount(1, $engine->sourceHandler()->getDirectoryList());

        $this->assertSame(
            [ '/application/tests/IoEngine/Console/Console' ],
            $engine->sourceHandler()->getDirectoryList()
        );

        $this->assertTrue($engine->isBooted());
    }
    
    private function makeMvcModule(): MvcModule
    {
        return new class extends MvcModule
        {
            public function bootDependencies(Container $container): void
            {
                // ...
            }

            public function bootRoutes(Router &$router): void
            {
            }
        };
    }

    private function makeFcModule(): FcModule
    {
        return new class extends FcModule
        {
            public function bootDependencies(Container $container): void
            {
                // ...
            }

            public function bootNamespaces(CommandSourceSet &$sourceSet): void
            {
                $sourceSet->add(new CommandSource('Tests\Run\Actions'));
            }
        };
    }

    private function makeConsoleModule(): ConsoleModule
    {
        return new class extends ConsoleModule
        {
            public function bootDependencies(Container $container): void
            {
                // ...
            }

            public function bootRoutineDirectories(RoutineSourceSet &$sourceSet): void
            {
                $sourceSet->add(new RoutineSource(__DIR__ . '/Console'));
            }

            public function getCommandName(): string
            {
                return 'test-script';
            }

            /** Devolve o diretório real da aplicação que implementa o Console */
            public function getCommandPath(): string
            {
                return __DIR__;
            }
        };
    }

    private function makeEngine(Container $container): ConsoleEngine
    {
        $engine = new ConsoleEngine();

        $engine->useContainer($container);

        $engine->useModuleSet(new ModuleSet());

        return $engine;
    }
}
