<?php

declare(strict_types=1);

namespace Tests\Run;

use Iquety\Application\IoEngine\Action\Input;
use Iquety\Application\IoEngine\Console\RoutineSource;
use Iquety\Application\IoEngine\Module;
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
