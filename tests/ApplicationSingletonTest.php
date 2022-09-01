<?php

declare(strict_types=1);

namespace Tests;

use ArrayObject;
use Freep\Application\Application;
use Freep\Application\Bootstrap;
use Freep\Application\Http\Request;
use Freep\Application\Http\Response;
use Freep\Application\Routing\Router;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

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
