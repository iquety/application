<?php

declare(strict_types=1);

namespace Tests\IoEngine\Mvc;

use Iquety\Application\IoEngine\ModuleSet;
use Iquety\Application\IoEngine\Mvc\MvcEngine;
use Iquety\Injection\Container;
use Tests\TestCase;

class MvcEngineBootTest extends TestCase
{
    /** @test */
    public function bootConsoleModule(): void
    {
        $engine = $this->makeEngine(new Container());

        $engine->boot($this->makeConsoleModuleOne(__DIR__ . '/Console'));

        $this->assertFalse($engine->isBooted());
    }

    /** @test */
    public function bootFcModule(): void
    {
        $engine = $this->makeEngine(new Container());

        $engine->boot($this->makeFcModuleOne('Tests\Run\Actions'));

        $this->assertFalse($engine->isBooted());
    }

    /** @test */
    public function bootMvcModule(): void
    {
        $engine = $this->makeEngine(new Container());

        $engine->boot($this->makeMvcModuleOne());

        $this->assertTrue($engine->isBooted());

        $this->assertTrue($engine->sourceHandler()->hasRoutes());
    }

    private function makeEngine(Container $container): MvcEngine
    {
        $engine = new MvcEngine();

        $engine->useContainer($container);

        $engine->useModuleSet(new ModuleSet());

        return $engine;
    }
}
