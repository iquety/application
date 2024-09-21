<?php

declare(strict_types=1);

namespace Tests\AppEngine\Mvc;

use Iquety\Application\AppEngine\Action\Input;
use Iquety\Application\AppEngine\ActionDescriptor;
use Iquety\Application\AppEngine\FrontController\CommandSourceSet;
use Iquety\Application\AppEngine\FrontController\FcBootstrap;
use Iquety\Application\AppEngine\ModuleSet;
use Iquety\Application\AppEngine\Mvc\Controller\MainController;
use Iquety\Application\AppEngine\Mvc\MvcBootstrap;
use Iquety\Application\AppEngine\Mvc\MvcEngine;
use Iquety\Injection\Container;
use Iquety\Routing\Router;
use RuntimeException;
use Tests\AppEngine\Mvc\Stubs\OneController;
use Tests\TestCase;

class MvcEngineTest extends TestCase
{
    /**
     * @test
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function bootInvalid(): void
    {
        $container = new Container();
        $moduleSet = new ModuleSet();

        $engine = new MvcEngine();
        $engine->useContainer($container);
        $engine->useModuleSet($moduleSet);

        $bootstrap = new class extends FcBootstrap {
            public function bootDependencies(Container $container): void
            {
                $container->addSingleton('signature-test', fn() => 'teste');
            }

            public function bootNamespaces(CommandSourceSet &$sourceSet): void
            {
                // nenhum diretório setado
            }
        };

        $engine->boot($bootstrap);

        $this->assertFalse($engine->isBooted());
    }

    /**
     * @test
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function resolveHome(): void
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
                // nenhuma rota setada
            }
        };

        $engine->boot($bootstrap);

        $descriptor = $engine->resolve(Input::fromString(''));

        $this->assertNotNull($descriptor);
        $this->assertSame(MainController::class . '::execute', $descriptor->action());
    }

    /**
     * @test
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function resolveRoutesException(): void
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

        $engine->resolve(Input::fromString('xxxxxx'));
    }

    /**
     * @test
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function resolveModulesException(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('At least one engine must be provided');

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

        $engine->resolve(Input::fromString('/user/33'));
    }

    /**
     * @test
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
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

        $this->assertInstanceOf(ActionDescriptor::class, $descriptor);

        $this->assertSame('main', $descriptor->module());
        $this->assertSame(MainController::class . '::execute', $descriptor->action());
    }

    /**
     * @test
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
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

    /**
     * @test
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
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

        $moduleSet->add($bootstrap);
        $engine->boot($bootstrap);

        $input = Input::fromString('user/22/edit');

        $this->assertSame([
            0 => 22,
            1 => 'edit'
        ], $input->toArray());

        $descriptor = $engine->resolve($input);

        $this->assertInstanceOf(ActionDescriptor::class, $descriptor);
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
