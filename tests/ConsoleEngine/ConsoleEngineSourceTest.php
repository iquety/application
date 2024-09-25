<?php

declare(strict_types=1);

namespace Tests\Run;

use Iquety\Application\IoEngine\Console\ConsoleEngine;
use Iquety\Application\IoEngine\ModuleSet;
use Iquety\Injection\Container;
use Tests\TestCase;

class ConsoleEngineSourceTest extends TestCase
{
    /** @test */
    public function singleton(): void
    {
        $engine = new ConsoleEngine();

        $engine->useContainer(new Container());

        $engine->useModuleSet(new ModuleSet());

        $this->assertSame(
            $engine->sourceHandler(),
            $engine->sourceHandler()
        );
    }
}
