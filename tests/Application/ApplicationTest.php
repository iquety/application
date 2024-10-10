<?php

declare(strict_types=1);

namespace Tests\Application;

use DateTimeZone;
use Iquety\Application\Application;
use Iquety\Application\Environment;
use Tests\TestCase;

/** @SuppressWarnings(PHPMD.StaticAccess) */
class ApplicationTest extends TestCase
{
    /** @test */
    public function singletonInstance(): void
    {
        $instanceOne = Application::instance();
        $instanceTwo = Application::instance();

        $this->assertSame($instanceOne, $instanceTwo);
    }

    /** @test */
    public function environment(): void
    {
        $application = Application::instance();

        $application->runIn(Environment::DEVELOPMENT);
        $this->assertSame(Environment::DEVELOPMENT, $application->runningMode());

        $application->runIn(Environment::CONSOLE);
        $this->assertSame(Environment::CONSOLE, $application->runningMode());

        $application->runIn(Environment::PRODUCTION);
        $this->assertSame(Environment::PRODUCTION, $application->runningMode());

        $application->runIn(Environment::STAGE);
        $this->assertSame(Environment::STAGE, $application->runningMode());

        $application->runIn(Environment::TESTING);
        $this->assertSame(Environment::TESTING, $application->runningMode());
    }

    /** @test */
    public function timeZone(): void
    {
        $application = Application::instance();

        $application->useTimeZone(new DateTimeZone('America/New_York'));

        $this->assertSame('America/New_York', $application->timeZone()->getName());
    }
}
