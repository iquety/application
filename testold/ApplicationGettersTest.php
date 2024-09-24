<?php

declare(strict_types=1);

namespace Tests;

use DateTimeZone;
use InvalidArgumentException;
use Iquety\Application\AppEngine\EngineSet;
use Iquety\Application\AppEngine\ModuleSet;
use Iquety\Application\Application;
use Iquety\Application\Environment;
use Iquety\Injection\Container;

class ApplicationGettersTest extends ApplicationCase
{
    /**
     * @test
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function makeInvalidUse(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Dependency id was not specified');
        
        $instance = Application::instance();

        $instance->make();
    }
}
