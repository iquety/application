<?php

declare(strict_types=1);

namespace Tests\AppEngine\Console;

use Iquety\Application\AppEngine\Action\Input;
use Iquety\Application\AppEngine\Console\ConsoleBootstrap;
use Iquety\Application\AppEngine\Console\ConsoleDescriptor;
use Iquety\Application\AppEngine\Console\ConsoleEngine;
use Iquety\Application\AppEngine\Console\RoutineSource;
use Iquety\Application\AppEngine\Console\RoutineSourceSet;
use Iquety\Application\AppEngine\FrontController\CommandSourceSet;
use Iquety\Application\AppEngine\FrontController\FcBootstrap;
use Iquety\Application\AppEngine\ModuleSet;
use Iquety\Injection\Container;
use RuntimeException;
use Tests\TestCase;

class ConsoleEngineTest extends TestCase
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

        $engine = new ConsoleEngine();
        $engine->useContainer($container);
        $engine->useModuleSet($moduleSet);

        // bootstrap diferente de ConsoleBootstrap será ignorado
        $bootstrap = new class extends FcBootstrap {
            public function bootDependencies(Container $container): void
            {
                $container->addSingleton('signature-test', fn() => 'teste');
            }

            public function bootNamespaces(CommandSourceSet &$sourceSet): void {}
        };

        $engine->boot($bootstrap);

        $this->assertFalse($engine->isBooted());
    }

    /** @test */
    public function scriptExecuted(): void
    {
        $descriptor = $this->executeScript('test-success');

        $this->assertStringContainsString("Rotina de teste executada", $descriptor->output());

        // @see https://www.cyberciti.biz/faq/linux-bash-exit-status-set-exit-statusin-bash/
        $this->assertSame(0, $descriptor->status());
    }

    /** @test */
    public function scriptException(): void
    {
        $descriptor = $this->executeScript('test-exception');

        $this->assertStringContainsString("Uma exceção foi lançada", $descriptor->output());

        // @see https://www.cyberciti.biz/faq/linux-bash-exit-status-set-exit-statusin-bash/
        $this->assertSame(126, $descriptor->status());
    }

    /** @test */
    public function scriptNotFound(): void
    {
        $descriptor = $this->executeScript('test-invalid');

        // scripts que não existem devolvem a Ajuda no terminal
        $this->assertStringContainsString("How to use", $descriptor->output());

        // @see https://www.cyberciti.biz/faq/linux-bash-exit-status-set-exit-statusin-bash/
        $this->assertSame(127, $descriptor->status());
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

        $engine = new ConsoleEngine();
        $engine->useContainer($container);
        $engine->useModuleSet($moduleSet);

        $bootstrap = new class extends ConsoleBootstrap {
            public function bootDependencies(Container $container): void
            {
                $container->addSingleton('signature-test', fn() => 'teste');
            }

            public function bootRoutineDirectories(RoutineSourceSet &$sourceSet): void
            {
                $sourceSet->add(new RoutineSource(__DIR__ . '/Stubs'));
            }

            public function getCommandName(): string
            {
                return 'meu-comando';
            }

            public function getCommandPath(): string
            {
                return __DIR__;
            }
        };

        $engine->boot($bootstrap);

        $engine->resolve(Input::fromConsoleArguments(['test-success']));
    }
    
    protected function executeScript(string $scriptCommand): ConsoleDescriptor
    {
        $engine = new ConsoleEngine();

        $container = new Container();
        $engine->useContainer($container);

        $bootstrap = new class extends ConsoleBootstrap {
            public function bootDependencies(Container $container): void
            {
                $container->addSingleton('signature-test', fn() => 'teste');
            }

            public function bootRoutineDirectories(RoutineSourceSet &$sourceSet): void
            {
                $sourceSet->add(new RoutineSource(__DIR__ . '/Stubs'));
            }

            public function getCommandName(): string
            {
                return 'meu-comando';
            }

            public function getCommandPath(): string
            {
                return __DIR__;
            }
        };

        $moduleSet = new ModuleSet();
        $moduleSet->add($bootstrap);
        $engine->useModuleSet($moduleSet);

        $engine->boot($bootstrap);

        return $engine->resolve(Input::fromConsoleArguments([$scriptCommand]));
    }
}
