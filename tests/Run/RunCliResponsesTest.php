<?php

declare(strict_types=1);

namespace Tests\Run;

use Iquety\Application\Environment;
use Iquety\Application\IoEngine\Console\ConsoleEngine;
use Iquety\Application\IoEngine\Console\ConsoleInput;
use Iquety\Application\IoEngine\Console\ConsoleModule;
use Iquety\Application\IoEngine\Console\ConsoleOutput;
use Iquety\Application\IoEngine\Console\RoutineSource;
use Iquety\Application\IoEngine\Console\RoutineSourceSet;
use Iquety\Application\IoEngine\EngineSet;
use Iquety\Application\IoEngine\Module;
use Iquety\Application\IoEngine\ModuleSet;
use Iquety\Application\RunCli;
use Iquety\Injection\Container;
use Tests\TestCase;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class RunCliResponsesTest extends TestCase
{
    /** @test */
    public function outputNotFound(): void
    {
        $container = $this->makeContainer();

        $response = $this->makeOutput($container, ['script-name', 'not-found']);

        $this->assertSame(127, $response->getStatusCode());
        $this->assertStringContainsString('How to use:', (string)$response->getBody());
    }

    /** @test */
    public function outputOk(): void
    {
        $container = $this->makeContainer();

        $response = $this->makeOutput($container, ['script-name', 'test-console']);

        $this->assertSame(0, $response->getStatusCode());
        $this->assertStringContainsString('Teste console', (string)$response->getBody());
    }

    /** @test */
    public function outputError(): void
    {
        $container = $this->makeContainer();

        $response = $this->makeOutput($container, ['script-name', 'test-error']);

        $this->assertSame(126, $response->getStatusCode());
        $this->assertStringContainsString('Console error', (string)$response->getBody());
    }

    /** @param array<int,string> $consoleArguments */
    private function makeOutput(
        Container $container,
        array $consoleArguments,
        ?Module $extraModule = null
    ): ConsoleOutput {
        return $this->makeRunnner($container, $extraModule)
            ->run(new ConsoleInput($consoleArguments));
    }

    private function makeRunnner(Container $container, ?Module $extraModule = null): RunCli
    {
        $module = new class extends ConsoleModule
        {
            public function bootDependencies(Container $container): void
            {
                // ...
            }

            public function bootRoutineDirectories(RoutineSourceSet &$sourceSet): void
            {
                $sourceSet->add(new RoutineSource(__DIR__ . '/Console'));
            }

            public function getScriptName(): string
            {
                return 'test-script';
            }

            /** Devolve o diretório real da aplicação que implementa o Console */
            public function getScriptPath(): string
            {
                return './';
            }
        };

        $moduleSet = new ModuleSet();
        $moduleSet->add($module);

        if ($extraModule !== null) {
            $moduleSet->add($extraModule);
        }

        $engine = new ConsoleEngine();
        $engine->useModuleSet($moduleSet);

        $engineSet = new EngineSet($container);
        $engineSet->add($engine);
        $engineSet->bootEnginesWith($module);

        if ($extraModule !== null) {
            $engineSet->bootEnginesWith($extraModule);
        }

        $runner = new RunCli(
            Environment::DEVELOPMENT,
            $container,
            $module,
            $engineSet
        );

        return $runner;
    }
}
