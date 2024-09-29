<?php

declare(strict_types=1);

namespace Tests\IoEngine\Console;

use InvalidArgumentException;
use Iquety\Application\IoEngine\Console\RoutineSource;
use Iquety\Application\IoEngine\Console\RoutineSourceSet;
use Iquety\Application\IoEngine\Module;
use Tests\TestCase;

class RoutineSourceSetTest extends TestCase
{
    /** @test */
    public function moduleClass(): void
    {
        $source = new RoutineSourceSet(Module::class);

        $this->assertSame(Module::class, $source->getModuleClass());
    }

    /** @test */
    public function hasSources(): void
    {
        $routine = new RoutineSource(__DIR__ . '/a');

        $source = new RoutineSourceSet(Module::class);

        $this->assertFalse($source->hasSources());

        $source->add($routine);

        $this->assertTrue($source->hasSources());

        $this->assertEquals(
            [ $routine->getIdentity() => $routine ],
            $source->toArray()
        );
    }

    /** @test */
    public function duplicatedSources(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The specified source already exists');

        $source = new RoutineSourceSet(Module::class);

        $this->assertFalse($source->hasSources());

        $source->add(new RoutineSource(__DIR__ . '/a'));
        $source->add(new RoutineSource(__DIR__ . '/a'));
    }
}
