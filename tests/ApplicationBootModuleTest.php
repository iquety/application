<?php

declare(strict_types=1);

namespace Tests;

use ArrayObject;
use Freep\Application\Application;
use Freep\Application\Routing\Router;
use stdClass;

class ApplicationBootModuleTest extends TestCase
{
    public function setUp(): void
    {
        Application::instance()->reset();
    }

    /** @test */
    public function bootModule(): void
    {
        $app = TestCase::applicationFactory();

        $app->bootModule(new ModuleBootstrap());

        $this->assertEquals([ 
            (new Router())->forModule(ModuleBootstrap::class)->get('/post/:id'),
            (new Router())->forModule(ModuleBootstrap::class)->post('/post/:id')    
        ], $app->router()->routes());

        $this->assertTrue($app->container()->has(ArrayObject::class));
        $this->assertTrue($app->container()->has(stdClass::class));
    }
}