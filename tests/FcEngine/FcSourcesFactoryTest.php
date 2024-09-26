<?php

declare(strict_types=1);

namespace Tests\FcEngine;

use Iquety\Application\Adapter\HttpFactory\DiactorosHttpFactory;
use Iquety\Application\IoEngine\Action\Input;
use Iquety\Application\IoEngine\ActionDescriptor;
use Iquety\Application\IoEngine\FrontController\CommandSourceSet;
use Iquety\Application\IoEngine\FrontController\FcSourceHandler;
use Iquety\Application\IoEngine\Module;
use Iquety\Routing\Router;
use RuntimeException;
use Tests\TestCase;

class MvcSourcesFactoryTest extends TestCase
{
    /** @test */
    public function noSourceHandler(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('No registered sources for getting commands');

        $handler = new FcSourceHandler();

        $handler->getDescriptorTo(Input::fromString('/'));
    }

    /** @test */
    public function noSourceHandlerRequestNoHome(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('No registered sources for getting commands');

        $handler = new FcSourceHandler();

        $handler->addSources(new CommandSourceSet(Module::class));

        $handler->getDescriptorTo(Input::fromString('/any'));
    }

    /** @test */
    public function noRoutesRequestHome(): void
    {
        $this->markTestIncomplete();

        // $handler = new MvcSourceHandler();

        // $handler->addRouter(new Router());

        // $descriptor = $handler->getDescriptorTo(Input::fromString('/'));

        // $this->assertInstanceOf(ActionDescriptor::class, $descriptor);

        // $this->assertSame('main', $descriptor->module());
        // $this->assertSame(Controller::class, $descriptor->type());
        // $this->assertSame(MainController::class . '::execute', $descriptor->action());
    }

    /** @test */
    public function noRoutesRequestNotFound(): void
    {
        $this->markTestIncomplete();

        // $router = new Router();
        // $router->any('/any');

        // $handler = new MvcSourceHandler();

        // $handler->addRouter($router);

        // $descriptor = $handler->getDescriptorTo(Input::fromString('/not-exists'));

        // $this->assertNull($descriptor);
    }

    /** @test */
    public function routeNotAction(): void
    {
        $this->markTestIncomplete();

        // $this->expectException(RuntimeException::class);
        // $this->expectExceptionMessage('The route found does not have a action');

        // $input = Input::fromRequest(
        //     (new DiactorosHttpFactory())->createServerRequest('POST', '/any')
        // );

        // $router = new Router();
        // $router->any('/any'); // não foi configurada uma ação correspondente

        // $handler = new MvcSourceHandler();

        // $handler->addRouter($router);

        // $handler->getDescriptorTo($input);
    }

    /** @test */
    public function routeProcessed(): void
    {
        $this->markTestIncomplete();

        // $input = Input::fromRequest(
        //     (new DiactorosHttpFactory())->createServerRequest('POST', '/any/33')
        // );

        // $router = new Router();
        // $router->any('/any/:id')->usingAction(OneController::class, 'execute');

        // $handler = new MvcSourceHandler();

        // $handler->addRouter($router);

        // $descriptor = $handler->getDescriptorTo($input);

        // $this->assertInstanceOf(ActionDescriptor::class, $descriptor);

        // $this->assertSame('all', $descriptor->module());
        // $this->assertSame(Controller::class, $descriptor->type());
        // $this->assertSame(OneController::class . '::execute', $descriptor->action());
    }

    /** @test */
    public function routeProcessedForModule(): void
    {
        $this->markTestIncomplete();

        // $input = Input::fromRequest(
        //     (new DiactorosHttpFactory())->createServerRequest('POST', '/any/33')
        // );

        // $router = new Router();
        // $router->forModule(Module::class);
        // $router->any('/any/:id')->usingAction(OneController::class, 'execute');

        // $handler = new MvcSourceHandler();

        // $handler->addRouter($router);

        // $descriptor = $handler->getDescriptorTo($input);

        // $this->assertInstanceOf(ActionDescriptor::class, $descriptor);

        // $this->assertSame(Module::class, $descriptor->module());
        // $this->assertSame(Controller::class, $descriptor->type());
        // $this->assertSame(OneController::class . '::execute', $descriptor->action());
    }
}
