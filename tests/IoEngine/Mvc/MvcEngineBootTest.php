<?php

declare(strict_types=1);

namespace Tests\IoEngine\Mvc;

use Iquety\Application\IoEngine\Console\ConsoleModule;
use Iquety\Application\IoEngine\Console\RoutineSource;
use Iquety\Application\IoEngine\Console\RoutineSourceSet;
use Iquety\Application\IoEngine\FrontController\CommandSource;
use Iquety\Application\IoEngine\FrontController\CommandSourceSet;
use Iquety\Application\IoEngine\FrontController\FcModule;
use Iquety\Application\IoEngine\ModuleSet;
use Iquety\Application\IoEngine\Mvc\MvcEngine;
use Iquety\Application\IoEngine\Mvc\MvcModule;
use Iquety\Injection\Container;
use Iquety\Routing\Router;
use Tests\TestCase;

class MvcEngineBootTest extends TestCase
{
    /** @test */
    public function bootConsoleModule(): void
    {
        $engine = $this->makeEngine(new Container());

        $engine->boot($this->makeConsoleModule());

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
    public function bootMvcModule(): void
    {
        $engine = $this->makeEngine(new Container());

        $engine->boot($this->makeMvcModule());

        $this->assertTrue($engine->isBooted());

        $this->assertTrue($engine->sourceHandler()->hasRoutes());
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
                $router->any('/');
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

    private function makeEngine(Container $container): MvcEngine
    {
        $engine = new MvcEngine();

        $engine->useContainer($container);

        $engine->useModuleSet(new ModuleSet());

        return $engine;
    }
}
