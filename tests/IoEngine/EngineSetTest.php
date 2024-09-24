<?php

declare(strict_types=1);

namespace Tests\IoEngine;

use Iquety\Application\IoEngine\EngineSet;
use Iquety\Injection\Container;
use RuntimeException;
use Tests\TestCase;

class EngineSetTest extends TestCase
{
    /** @test */
    public function duplicatedEngine(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            'To return the handler, you must add at least one engine'
        );

        $container = new Container();

        $engineSet = new EngineSet($container);

        $engineSet->sourceHandler();
    }
}
