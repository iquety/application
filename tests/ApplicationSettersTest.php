<?php

declare(strict_types=1);

namespace Tests;

use DateTimeZone;
use Iquety\Application\AppEngine\EngineSet;
use Iquety\Application\AppEngine\ModuleSet;
use Iquety\Application\Application;
use Iquety\Application\Environment;
use Iquety\Injection\Container;

class ApplicationSettersTest extends ApplicationCase
{
    /** @return array<string,array<int,mixed>> */
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
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function environmentProduction(Environment $environment): void
    {
        $instance = Application::instance();

        $instance->runIn($environment);

        $this->assertSame($environment, $instance->runningMode());
    }

    /**
     * @test
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function timezone(): void
    {
        $instance = Application::instance();

        $instance->useTimezone(new DateTimeZone('America/Sao_Paulo'));

        $this->assertEquals(
            new DateTimeZone('America/Sao_Paulo'),
            $instance->timezone()
        );
    }

    /**
     * @test
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function composition(): void
    {
        $instance = Application::instance();

        $this->assertInstanceOf(Container::class, $instance->container());
        $this->assertInstanceOf(EngineSet::class, $instance->engineSet());
        $this->assertInstanceOf(ModuleSet::class, $instance->moduleSet());
    }
}
