<?php

declare(strict_types=1);

namespace Tests\IoEngine\Mvc;

use Iquety\Application\IoEngine\Mvc\MvcSourceHandler;
use Iquety\Routing\Router;
use Tests\TestCase;

class MvcSourcesRoutesTest extends TestCase
{
    /** @test */
    public function routes(): void
    {
        $router = new Router();

        $handler = new MvcSourceHandler();

        $handler->addRouter($router);

        $this->assertFalse($handler->hasRoutes());

        $router->post('/');

        $this->assertTrue($handler->hasRoutes());
    }
}
