<?php

declare(strict_types=1);

namespace Tests\Unit\AppEngine;

use InvalidArgumentException;
use Iquety\Application\AppEngine\AppEngine;
use Iquety\Application\AppEngine\EngineSet;
use Iquety\Injection\Container;
use Tests\Unit\TestCase;

class EngineSetTest extends TestCase
{
    /** @test */
    public function duplicatedEngine(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The same engine cannot be added twice');

        $container = new Container();

        $engineSet = new EngineSet($container);

        $engine = $this->createMock(AppEngine::class);

        $engineSet->add($engine);
        $engineSet->add($engine);
    }

    /** @test */
    public function distinctEngines(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The same engine cannot be added twice');
        
        $container = new Container();

        $engineSet = new EngineSet($container);

        $engineOne = $this->createMock(AppEngine::class);
        $engineTwo = $this->createMock(AppEngine::class);

        $engineSet->add($engineOne);
        $engineSet->add($engineTwo);

        $this->assertCount(2, $engineSet->toArray());
    }
}
