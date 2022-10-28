<?php

declare(strict_types=1);

namespace Tests;

use Iquety\Application\Application;
use RuntimeException;


/** @SuppressWarnings(PHPMD.StaticAccess) */
class ApplicationRunTest extends TestCase
{
    public function setUp(): void
    {
        Application::instance()->reset();
    }

    /** @test */
    public function runWithoutEngine(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('No web engine to handle the request');

        $app = TestCase::applicationFactory();

        $app->run();
    }
}
