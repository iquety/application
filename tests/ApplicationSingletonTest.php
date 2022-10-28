<?php

declare(strict_types=1);

namespace Tests;

use ArrayObject;
use Iquety\Application\Application;

/** @SuppressWarnings(PHPMD.StaticAccess) */
class ApplicationSingletonTest extends TestCase
{
    public function setUp(): void
    {
        Application::instance()->reset();
    }

    /** @test */
    public function sigleton(): void
    {
        $this->assertNotSame(new ArrayObject(), new ArrayObject());
        $this->assertSame(Application::instance(), Application::instance());
    }
}
