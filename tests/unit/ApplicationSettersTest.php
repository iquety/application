<?php

declare(strict_types=1);

namespace Tests\Unit;

use DateTimeZone;
use Exception;
use Iquety\Application\AppEngine\EngineSet;
use Iquety\Application\AppEngine\ModuleSet;
use Iquety\Application\AppEngine\Mvc\MvcBootstrap;
use Iquety\Application\AppEngine\Mvc\MvcEngine;
use Iquety\Application\Application;
use Iquety\Application\Environment;
use Iquety\Injection\Container;
use Iquety\Routing\Router;
use RuntimeException;

class ApplicationSettersTest extends ApplicationCase
{
    public function environmentProvider(): array
    {
        $list = [];

        $list['production']  = [Environment::PRODUCTION];
        $list['testing']     = [Environment::TESTING];
        $list['stage']       = [Environment::STAGE];
        $list['development'] = [Environment::DEVELOPMENT];

        return $list;
    }

    /**
     * @test 
     * @dataProvider environmentProvider
     */
    public function environmentProduction(Environment $environment): void
    {
        $instance = Application::instance();

        $instance->runIn($environment);

        $this->assertSame($environment, $instance->runningMode());
    }

    /** @test */
    public function timezone(): void
    {
        $instance = Application::instance();

        $instance->useTimezone(new DateTimeZone('America/Sao_Paulo'));

        $this->assertEquals(
            new DateTimeZone('America/Sao_Paulo'),
            $instance->timezone()
        );
    }

    /** @test */
    public function composition(): void
    {
        $instance = Application::instance();

        $this->assertInstanceOf(Container::class, $instance->container());
        $this->assertInstanceOf(EngineSet::class, $instance->engineSet());
        $this->assertInstanceOf(ModuleSet::class, $instance->moduleSet());
    }
}
