<?php

declare(strict_types=1);

namespace Tests\AppEngine\Mvc;

use ArrayObject;
use Closure;
use InvalidArgumentException;
use Iquety\Application\AppEngine\Action\Input;
use Iquety\Application\AppEngine\ActionDescriptor;
use Iquety\Application\AppEngine\Mvc\Controller\Controller;
use Iquety\Application\AppEngine\Mvc\Controller\ErrorController;
use Iquety\Application\AppEngine\Mvc\Controller\MainController;
use Iquety\Application\AppEngine\Mvc\Controller\NotFoundController;
use Iquety\Application\AppEngine\Mvc\MvcBootstrap;
use Iquety\Application\AppEngine\Mvc\MvcSourceHandler;
use Iquety\Routing\Router;
use RuntimeException;
use Tests\AppEngine\Mvc\Stubs\OneController;
use Tests\TestCase;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class MvcSourceHandlerTest extends TestCase
{
    /** @test */
    public function controllerGetDefaults(): void
    {
        $handler = new MvcSourceHandler();

        $this->assertEquals(
            new ActionDescriptor(Controller::class, 'error', ErrorController::class, 'execute'),
            $handler->getErrorDescriptor()
        );

        $this->assertEquals(
            new ActionDescriptor(Controller::class, 'main', MainController::class, 'execute'),
            $handler->getMainDescriptor()
        );

        $this->assertEquals(
            new ActionDescriptor(Controller::class, 'not-found', NotFoundController::class, 'execute'),
            $handler->getNotFoundDescriptor()
        );
    }

    /** @test */
    public function controllerSetters(): void
    {
        $handler = new MvcSourceHandler();

        $handler->setErrorActionClass(OneController::class);
        $handler->setMainActionClass(OneController::class);
        $handler->setNotFoundActionClass(OneController::class);

        $this->assertEquals(
            new ActionDescriptor(Controller::class, 'error', OneController::class, 'execute'),
            $handler->getErrorDescriptor()
        );

        $this->assertEquals(
            new ActionDescriptor(Controller::class, 'main', OneController::class, 'execute'),
            $handler->getMainDescriptor()
        );

        $this->assertEquals(
            new ActionDescriptor(Controller::class, 'not-found', OneController::class, 'execute'),
            $handler->getNotFoundDescriptor()
        );
    }

    /** @test */
    public function controllerSetInvalidError(): void
    {
        $className = ArrayObject::class;

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Class $className is not a valid controller");

        $handler = new MvcSourceHandler();

        $handler->setErrorActionClass($className);
    }

    /** @test */
    public function controllerSetInvalidMain(): void
    {
        $className = ArrayObject::class;

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Class $className is not a valid controller");

        $handler = new MvcSourceHandler();

        $handler->setMainActionClass($className);
    }

    /** @test */
    public function controllerSetInvalidNotFound(): void
    {
        $className = ArrayObject::class;

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Class $className is not a valid controller");

        $handler = new MvcSourceHandler();

        $handler->setNotFoundActionClass($className);
    }

    /** @test */
    public function descriptorWithoutRouter(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('No router specified');

        $handler = new MvcSourceHandler();

        $handler->getDescriptorTo(Input::fromString('uri/da/rota'));
    }

    /** @test */
    public function descriptorWithoutEmptyRouter(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('There are no registered routes');

        $handler = new MvcSourceHandler();
        $handler->addRouter(new Router());

        $handler->getDescriptorTo(Input::fromString('uri/da/rota'));
    }

    /** @test */
    public function descriptorNotFound(): void
    {
        $router = new Router();

        $router->get('/user/:id')->usingAction(OneController::class);

        $handler = new MvcSourceHandler();

        $handler->addRouter($router);

        $this->assertNull(
            $handler->getDescriptorTo(Input::fromString('uri/inexistente'))
        );
    }

    /** @return array<string,array<int,mixed>> */
    public function mainUriProvider(): array
    {
        $list = [];

        $list['empty'] = [ '' ];

        $list['empty space'] = [ ' ' ];

        $list['bar'] = [ '/' ];
        $list['bar start space'] = [ ' /' ];
        $list['bar finish space'] = [ '/ ' ];
        $list['bar both spaces'] = [ ' / ' ];

        return $list;
    }

    /**
     * @test
     * @dataProvider mainUriProvider
     */
    public function descriptorMain(string $uri): void
    {
        $router = new Router();

        $router->get('/user/:id')->usingAction(OneController::class);

        $handler = new MvcSourceHandler();

        $handler->addRouter($router);

        $this->assertEquals(
            new ActionDescriptor(Controller::class, 'main', MainController::class, 'execute'),
            $handler->getDescriptorTo(Input::fromString($uri))
        );
    }

    /** @test */
    public function routeWithoutAction(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The route found does not have a action');

        $router = new Router();

        $router->get('/user/:id');

        $handler = new MvcSourceHandler();

        $handler->addRouter($router);

        $handler->getDescriptorTo(Input::fromString('user/22'));
    }

    /** @test */
    public function descriptorController(): void
    {
        $router = new Router();
        $router->forModule(MvcBootstrap::class);

        $router->get('/user/:id')->usingAction(OneController::class, 'action');

        $handler = new MvcSourceHandler();

        $handler->addRouter($router);

        $this->assertEquals(
            new ActionDescriptor(Controller::class, MvcBootstrap::class, OneController::class, 'action'),
            $handler->getDescriptorTo(Input::fromString('user/22'))
        );
    }

    /** @test */
    public function descriptorClosure(): void
    {
        $router = new Router();
        $router->forModule(MvcBootstrap::class);

        $router->get('/user/:id')->usingAction(fn() => 'execute closure');

        $handler = new MvcSourceHandler();

        $handler->addRouter($router);

        $descriptor = $handler->getDescriptorTo(Input::fromString('user/22'));

        $this->assertInstanceOf(ActionDescriptor::class, $descriptor);

        $callable = $descriptor->action();

        $this->assertInstanceOf(Closure::class, $callable);

        $this->assertSame('execute closure', $callable());
    }
}
