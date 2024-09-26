<?php

declare(strict_types=1);

namespace Tests\FcEngine;

use Iquety\Application\IoEngine\FrontController\FcEngine;
use Iquety\Application\IoEngine\ModuleSet;
use Iquety\Injection\Container;
use Tests\TestCase;

class FcEngineSourceTest extends TestCase
{
    /** @test */
    public function singleton(): void
    {
        $engine = new FcEngine();

        $engine->useContainer(new Container());

        $engine->useModuleSet(new ModuleSet());

        $this->assertSame(
            $engine->sourceHandler(),
            $engine->sourceHandler()
        );
    }
}
