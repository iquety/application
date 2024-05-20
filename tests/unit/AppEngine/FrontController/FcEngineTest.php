<?php

declare(strict_types=1);

namespace Tests\Unit\AppEngine\FrontController;

use Exception;
use Iquety\Application\Adapter\HttpFactory\DiactorosHttpFactory;
use Iquety\Application\AppEngine\FrontController\Command\MainCommand;
use Iquety\Application\AppEngine\FrontController\Directory;
use Iquety\Application\AppEngine\FrontController\DirectorySet;
use Iquety\Application\AppEngine\FrontController\FcBootstrap;
use Iquety\Application\AppEngine\FrontController\FcEngine;
use Iquety\Application\AppEngine\Input;
use Iquety\Application\AppEngine\ModuleSet;
use Iquety\Application\Application;
use Iquety\Application\Http\HttpFactory;
use Iquety\Injection\Container;
use RuntimeException;
use Tests\Unit\AppEngine\FrontController\Stubs\Commands\FailCommand;
use Tests\Unit\AppEngine\FrontController\Stubs\Commands\SubDirectory\TwoCommand;
use Tests\Unit\TestCase;

class FcEngineTest extends TestCase
{
    /** @test */
    public function resolveException(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('No directories registered as command source');

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

            public function bootDirectories(DirectorySet &$directorySet): void
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

            public function bootDirectories(DirectorySet &$directorySet): void
            {
                $directorySet->add(new Directory(
                    'Tests\Unit\AppEngine\FrontController\Stubs\Commands'
                ));
            }
        };

        $engine->boot($bootstrap);

        $descriptor = $engine->resolve(Input::fromString(''));

        $this->assertSame('main', $descriptor->module());
        $this->assertSame(MainCommand::class . '::execute', $descriptor->action());
    }

    /** @test */
    public function resolveNotFound(): void
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

            public function bootDirectories(DirectorySet &$directorySet): void
            {
                $directorySet->add(new Directory(
                    'Tests\Unit\AppEngine\FrontController\Stubs\Commands'
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

            public function bootDirectories(DirectorySet &$directorySet): void
            {
                $directorySet->add(new Directory(
                    'Tests\Unit\AppEngine\FrontController\Stubs\Commands'
                ));
            }
        };

        $engine->boot($bootstrap);

        $descriptor = $engine->resolve(Input::fromString('sub-directory/two-command'));

        $this->assertSame($bootstrap::class, $descriptor->module());
        $this->assertSame(TwoCommand::class . '::execute', $descriptor->action());
    }
}
