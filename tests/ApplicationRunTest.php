<?php

declare(strict_types=1);

namespace Tests;

use Freep\Application\Application;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;
use Tests\Support\Mvc\UserArrayClosureActionBootstrap;
use Tests\Support\Mvc\UserBootstrap;
use Tests\Support\Mvc\UserClosureActionBootstrap;
use Tests\Support\Mvc\UserNoActionBootstrap;
use Tests\Support\Mvc\UserNullClosureActionBootstrap;
use Tests\Support\Mvc\UserRestrictedBootstrap;
use Tests\Support\Mvc\UserStringClosureActionBootstrap;

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
