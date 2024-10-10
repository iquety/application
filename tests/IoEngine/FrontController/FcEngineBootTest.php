<?php

declare(strict_types=1);

namespace Tests\IoEngine\FrontController;

use Iquety\Application\Http\HttpMethod;
use Iquety\Application\IoEngine\FrontController\FcEngine;
use Iquety\Application\IoEngine\ModuleSet;
use Iquety\Injection\Container;
use Tests\TestCase;

class FcEngineBootTest extends TestCase
{
    /** @test */
    public function bootFcModule(): void
    {
        $engine = $this->makeEngine(new Container());

        $engine->boot($this->makeFcModuleOne('Tests\Run\Actions'));

        $this->assertTrue($engine->isBooted());

        $this->assertTrue($engine->sourceHandler()->hasSources());
    }

    /** @test */
    public function bootConsoleModule(): void
    {
        $engine = $this->makeEngine(new Container());

        $engine->boot($this->makeConsoleModuleOne());

        $this->assertFalse($engine->isBooted());
    }

    /** @test */
    public function bootMvcModule(): void
    {
        $engine = $this->makeEngine(new Container());

        $engine->boot($this->makeMvcModuleOne(HttpMethod::ANY, '/'));

        $this->assertFalse($engine->isBooted());
    }

    private function makeEngine(Container $container): FcEngine
    {
        $engine = new FcEngine();

        $engine->useContainer($container);

        $engine->useModuleSet(new ModuleSet());

        return $engine;
    }
}
