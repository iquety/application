<?php

declare(strict_types=1);

namespace Tests\ConsoleEngine;

use Iquety\Application\IoEngine\Console\RoutineSource;
use Tests\TestCase;

class RoutineSourceTest extends TestCase
{
    /** @test */
    public function directoryValue(): void
    {
        $routine = new RoutineSource(__DIR__);

        $this->assertSame(__DIR__, $routine->getDirectory());
        $this->assertSame(md5(__DIR__), $routine->getIdentity());
    }
}
