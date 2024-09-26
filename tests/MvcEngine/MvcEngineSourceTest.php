<?php

declare(strict_types=1);

namespace Tests\MvcEngine;

use Iquety\Application\IoEngine\ModuleSet;
use Iquety\Application\IoEngine\Mvc\MvcEngine;
use Iquety\Injection\Container;
use Tests\TestCase;

class MvcEngineSourceTest extends TestCase
{
    /** @test */
    public function singleton(): void
    {
        $engine = new MvcEngine();

        $engine->useContainer(new Container());

        $engine->useModuleSet(new ModuleSet());

        $this->assertSame(
            $engine->sourceHandler(),
            $engine->sourceHandler()
        );
    }
}
