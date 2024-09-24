<?php

declare(strict_types=1);

namespace Tests\IoEngine\FrontController;

use Iquety\Application\IoEngine\FrontController\Command\MainCommand;
use Iquety\Application\IoEngine\FrontController\CommandSourceSet;
use Iquety\Application\IoEngine\FrontController\FcBootstrap;
use Iquety\Application\IoEngine\FrontController\FcEngine;
use Iquety\Application\IoEngine\FrontController\CommandSource;
use Iquety\Application\IoEngine\Action\Input;
use Iquety\Application\IoEngine\ActionDescriptor;
use Iquety\Application\IoEngine\ModuleSet;
use Iquety\Application\IoEngine\Mvc\MvcBootstrap;
use Iquety\Injection\Container;
use Iquety\Routing\Router;
use RuntimeException;
use Tests\IoEngine\FrontController\Stubs\Commands\SubDirectory\TwoCommand;
use Tests\TestCase;

/** @SuppressWarnings(PHPMD.StaticAccess) */
class FcEngineTest extends TestCase
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

        $engine = new FcEngine();
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

        $this->assertFalse($engine->isBooted());
    }

    /**
     * @test
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function resolveException(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('No registered sources for getting commands');

        $container = new Container();
        $moduleSet = new ModuleSet();

        $engine = new FcEngine();
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

        $engine->resolve(Input::fromString(''));
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

        $engine = new FcEngine();
        $engine->useContainer($container);
        $engine->useModuleSet($moduleSet);

        $bootstrap = new class extends FcBootstrap {
            public function bootDependencies(Container $container): void
            {
                $container->addSingleton('signature-test', fn() => 'teste');
            }

            public function bootNamespaces(CommandSourceSet &$sourceSet): void
            {
                $sourceSet->add(new CommandSource(
                    'Tests\IoEngine\FrontController\Stubs\Commands'
                ));
            }
        };

        $engine->boot($bootstrap);

        $engine->resolve(Input::fromString('sub-directory/two-command'));
    }

    /** @test */
    public function resolveHome(): void
    {
        $container = new Container();
        $moduleSet = new ModuleSet();

        $engine = new FcEngine();
        $engine->useContainer($container);
        $engine->useModuleSet($moduleSet);

        $bootstrap = new class extends FcBootstrap {
            public function bootDependencies(Container $container): void
            {
                $container->addSingleton('signature-test', fn() => 'teste');
            }

            public function bootNamespaces(CommandSourceSet &$sourceSet): void
            {
                $sourceSet->add(new CommandSource(
                    'Tests\IoEngine\FrontController\Stubs\Commands'
                ));
            }
        };

        $engine->boot($bootstrap);

        $descriptor = $engine->resolve(Input::fromString(''));

        $this->assertInstanceOf(ActionDescriptor::class, $descriptor);
        $this->assertSame('main', $descriptor->module());
        $this->assertSame(MainCommand::class . '::execute', $descriptor->action());
    }

    /** @test */
    public function resolveNull(): void
    {
        $container = new Container();
        $moduleSet = new ModuleSet();

        $engine = new FcEngine();
        $engine->useContainer($container);
        $engine->useModuleSet($moduleSet);

        $bootstrap = new class extends FcBootstrap {
            public function bootDependencies(Container $container): void
            {
                $container->addSingleton('signature-test', fn() => 'teste');
            }

            public function bootNamespaces(CommandSourceSet &$sourceSet): void
            {
                $sourceSet->add(new CommandSource(
                    'Tests\IoEngine\FrontController\Stubs\Commands'
                ));
            }
        };

        $engine->boot($bootstrap);

        $this->assertNull($engine->resolve(Input::fromString('not-found')));
    }

    /** @test */
    public function resolveCommand(): void
    {
        $container = new Container();
        $moduleSet = new ModuleSet();

        $engine = new FcEngine();
        $engine->useContainer($container);
        $engine->useModuleSet($moduleSet);

        $bootstrap = new class extends FcBootstrap {
            public function bootDependencies(Container $container): void
            {
                $container->addSingleton('signature-test', fn() => 'teste');
            }

            public function bootNamespaces(CommandSourceSet &$sourceSet): void
            {
                $sourceSet->add(new CommandSource(
                    'Tests\IoEngine\FrontController\Stubs\Commands'
                ));
            }
        };

        $moduleSet->add($bootstrap);
        $engine->boot($bootstrap);

        $descriptor = $engine->resolve(Input::fromString('sub-directory/two-command'));

        $this->assertInstanceOf(ActionDescriptor::class, $descriptor);
        $this->assertSame($bootstrap::class, $descriptor->module());
        $this->assertSame(TwoCommand::class . '::execute', $descriptor->action());
    }
}
