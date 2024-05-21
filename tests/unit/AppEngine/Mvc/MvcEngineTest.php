<?php

declare(strict_types=1);

namespace Tests\Unit\AppEngine\FrontController;

use Iquety\Application\AppEngine\Input;
use Iquety\Application\AppEngine\ModuleSet;
use Iquety\Application\AppEngine\Mvc\Controller\MainController;
use Iquety\Application\AppEngine\Mvc\MvcBootstrap;
use Iquety\Application\AppEngine\Mvc\MvcEngine;
use Iquety\Injection\Container;
use Iquety\Routing\Router;
use RuntimeException;
use Tests\Unit\AppEngine\Mvc\Stubs\OneController;
use Tests\Unit\TestCase;

class MvcEngineTest extends TestCase
{
    /** @test */
    public function resolveException(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('There are no registered routes');

        $container = new Container();
        $moduleSet = new ModuleSet();

        $engine = new MvcEngine();
        $engine->useContainer($container);
        $engine->useModuleSet($moduleSet);

        $bootstrap = new class extends MvcBootstrap {
            public function bootDependencies(Container $container): void
            {
                $container->addSingleton('signature-test', fn() => 'teste');
            }

            public function bootRoutes(Router &$router): void
            {
                // nenhuma rota setada
            }
        };

        $engine->boot($bootstrap);

        $engine->resolve(Input::fromString(''));
    }

    /** @test */
    public function resolveMain(): void
    {
        $container = new Container();
        $moduleSet = new ModuleSet();

        $engine = new MvcEngine();
        $engine->useContainer($container);
        $engine->useModuleSet($moduleSet);

        $bootstrap = new class extends MvcBootstrap {
            public function bootDependencies(Container $container): void
            {
                $container->addSingleton('signature-test', fn() => 'teste');
            }

            public function bootRoutes(Router &$router): void
            {
                $router->get('/user/:id')->usingAction(OneController::class, 'action');
            }
        };

        $engine->boot($bootstrap);

        $descriptor = $engine->resolve(Input::fromString(''));

        $this->assertSame('main', $descriptor->module());
        $this->assertSame(MainController::class . '::execute', $descriptor->action());
    }

    /** @test */
    public function resolveNull(): void
    {
        $container = new Container();
        $moduleSet = new ModuleSet();

        $engine = new MvcEngine();
        $engine->useContainer($container);
        $engine->useModuleSet($moduleSet);

        $bootstrap = new class extends MvcBootstrap {
            public function bootDependencies(Container $container): void
            {
                $container->addSingleton('signature-test', fn() => 'teste');
            }

            public function bootRoutes(Router &$router): void
            {
                $router->get('/user/:id')->usingAction(OneController::class, 'action');
            }
        };

        $engine->boot($bootstrap);

        $this->assertNull($engine->resolve(Input::fromString('not-found')));
    }

    /** @test */
    public function resolveController(): void
    {
        $container = new Container();
        $moduleSet = new ModuleSet();

        $engine = new MvcEngine();
        $engine->useContainer($container);
        $engine->useModuleSet($moduleSet);

        $bootstrap = new class extends MvcBootstrap {
            public function bootDependencies(Container $container): void
            {
                $container->addSingleton('signature-test', fn() => 'teste');
            }

            public function bootRoutes(Router &$router): void
            {
                $router->get('/user/:id/edit')->usingAction(OneController::class, 'action');
            }
        };

        $engine->boot($bootstrap);

        $input = Input::fromString('user/22/edit');

        $this->assertSame([
            0 => 22,
            1 => 'edit'
        ], $input->toArray());

        $descriptor = $engine->resolve($input);

        $this->assertSame($bootstrap::class, $descriptor->module());
        $this->assertSame(OneController::class . '::action', $descriptor->action());

        // input recebe os parâmetros do roteador na resolução
        $this->assertSame([
            0 => 22,
            1 => 'edit',
            'id' => 22
        ], $input->toArray());
    }
}
