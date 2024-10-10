<?php

declare(strict_types=1);

namespace Tests\IoEngine\Mvc;

use Iquety\Application\IoEngine\Action\Input;
use Iquety\Application\IoEngine\Action\ActionDescriptor;
use Iquety\Application\IoEngine\Module;
use Iquety\Application\IoEngine\Mvc\Controller\Controller;
use Iquety\Application\IoEngine\Mvc\Controller\MainController;
use Iquety\Application\IoEngine\Mvc\MvcSourceHandler;
use Iquety\Routing\Router;
use RuntimeException;
use Tests\IoEngine\Mvc\Stubs\OneController;
use Tests\TestCase;

/** @SuppressWarnings(PHPMD.StaticAccess) */
class MvcSourcesFactoryTest extends TestCase
{
    /** @test */
    public function noRouter(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('No router specified');

        $handler = new MvcSourceHandler();

        $handler->getDescriptorTo(Input::fromString('/'));
    }

    /** @test */
    public function noRoutesRequestNoHome(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('There are no registered routes');

        $handler = new MvcSourceHandler();

        $handler->addRouter(new Router());

        $handler->getDescriptorTo(Input::fromString('/any'));
    }

    /** @test */
    public function noRoutesRequestHome(): void
    {
        $handler = new MvcSourceHandler();

        $handler->addRouter(new Router());

        $descriptor = $handler->getDescriptorTo(Input::fromString('/'));

        $this->assertInstanceOf(ActionDescriptor::class, $descriptor);

        $this->assertSame('main', $descriptor->module());
        $this->assertSame(Controller::class, $descriptor->type());
        $this->assertSame(MainController::class . '::execute', $descriptor->action());
    }

    /** @test */
    public function noRoutesRequestNotFound(): void
    {
        $router = new Router();
        $router->any('/any');

        $handler = new MvcSourceHandler();

        $handler->addRouter($router);

        $descriptor = $handler->getDescriptorTo(Input::fromString('/not-exists'));

        $this->assertNull($descriptor);
    }

    /** @test */
    public function routeNotAction(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The route found does not have a action');

        $input = Input::fromRequest(
            $this->makeHttpFactory()->createServerRequest('POST', '/any')
        );

        $router = new Router();
        $router->any('/any'); // não foi configurada uma ação correspondente

        $handler = new MvcSourceHandler();

        $handler->addRouter($router);

        $handler->getDescriptorTo($input);
    }

    /** @test */
    public function routeProcessed(): void
    {
        $input = Input::fromRequest(
            $this->makeHttpFactory()->createServerRequest('POST', '/any/33')
        );

        $router = new Router();
        $router->any('/any/:id')->usingAction(OneController::class, 'execute');

        $handler = new MvcSourceHandler();

        $handler->addRouter($router);

        $descriptor = $handler->getDescriptorTo($input);

        $this->assertInstanceOf(ActionDescriptor::class, $descriptor);

        $this->assertSame('all', $descriptor->module());
        $this->assertSame(Controller::class, $descriptor->type());
        $this->assertSame(OneController::class . '::execute', $descriptor->action());
    }

    /** @test */
    public function routeProcessedForModule(): void
    {
        $input = Input::fromRequest(
            $this->makeHttpFactory()->createServerRequest('POST', '/any/33')
        );

        $router = new Router();
        $router->forModule(Module::class);
        $router->any('/any/:id')->usingAction(OneController::class, 'execute');

        $handler = new MvcSourceHandler();

        $handler->addRouter($router);

        $descriptor = $handler->getDescriptorTo($input);

        $this->assertInstanceOf(ActionDescriptor::class, $descriptor);

        $this->assertSame(Module::class, $descriptor->module());
        $this->assertSame(Controller::class, $descriptor->type());
        $this->assertSame(OneController::class . '::execute', $descriptor->action());
    }
}
