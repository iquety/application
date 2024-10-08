<?php

declare(strict_types=1);

namespace Tests\IoEngine\Console;

use Iquety\Application\IoEngine\Action\Input;
use Iquety\Application\IoEngine\Console\ConsoleEngine;
use Iquety\Application\IoEngine\ModuleSet;
use Iquety\Injection\Container;
use RuntimeException;
use Tests\TestCase;

class ConsoleEngineResolveTest extends TestCase
{
    /**
     * @test
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function resolve(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            'At least one Routine Source Set must be added via addSource'
        );

        $engine = new ConsoleEngine();

        $engine->useContainer(new Container());
        $engine->useModuleSet(new ModuleSet());

        $engine->resolve(Input::fromString('/'));
    }
}
