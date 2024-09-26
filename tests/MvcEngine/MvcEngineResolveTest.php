<?php

declare(strict_types=1);

namespace Tests\MvcEngine;

use Iquety\Application\IoEngine\Action\Input;
use Iquety\Application\IoEngine\ModuleSet;
use Iquety\Application\IoEngine\Mvc\MvcEngine;
use Iquety\Application\IoEngine\Mvc\MvcSourceHandler;
use Iquety\Injection\Container;
use Iquety\Routing\Router;
use RuntimeException;
use Tests\IoEngine\Stubs\Mvc\AnyController;
use Tests\TestCase;

class MvcEngineResolveTest extends TestCase
{
    /** @test */
    public function resolveWithoutModules(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('At least one module must be provided');

        $router = new Router();
        $router->any('/any')->usingAction(AnyController::class);

        $handler = new MvcSourceHandler();
        $handler->addRouter($router);

        $container = new Container();
        $container->addSingleton(MvcSourceHandler::class, $handler);

        $engine = new MvcEngine();
        $engine->useContainer($container);
        $engine->useModuleSet(new ModuleSet);

        $engine->resolve(Input::fromString('/any'));
    }
}
