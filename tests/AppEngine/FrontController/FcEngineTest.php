<?php

declare(strict_types=1);

namespace Tests\AppEngine\FrontController;

use Iquety\Application\AppEngine\FrontController\Command\MainCommand;
use Iquety\Application\AppEngine\FrontController\CommandSourceSet;
use Iquety\Application\AppEngine\FrontController\FcBootstrap;
use Iquety\Application\AppEngine\FrontController\FcEngine;
use Iquety\Application\AppEngine\FrontController\CommandSource;
use Iquety\Application\AppEngine\Action\Input;
use Iquety\Application\AppEngine\ActionDescriptor;
use Iquety\Application\AppEngine\ModuleSet;
use Iquety\Injection\Container;
use RuntimeException;
use Tests\AppEngine\FrontController\Stubs\Commands\SubDirectory\TwoCommand;
use Tests\TestCase;

/** @SuppressWarnings(PHPMD.StaticAccess) */
class FcEngineTest extends TestCase
{
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

    /** @test */
    public function resolveMain(): void
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
                    'Tests\AppEngine\FrontController\Stubs\Commands'
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
                    'Tests\AppEngine\FrontController\Stubs\Commands'
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
                    'Tests\AppEngine\FrontController\Stubs\Commands'
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
