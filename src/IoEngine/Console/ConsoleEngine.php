<?php

declare(strict_types=1);

namespace Iquety\Application\IoEngine\Console;

use Iquety\Application\IoEngine\Action\ActionDescriptor;
use Iquety\Application\IoEngine\IoEngine;
use Iquety\Application\IoEngine\Action\Input;
use Iquety\Application\IoEngine\Module;
use Iquety\Application\IoEngine\SourceHandler;
use Iquety\Console\Terminal;
use RuntimeException;

/** @SuppressWarnings(PHPMD.CouplingBetweenObjects) */
class ConsoleEngine extends IoEngine
{
    private bool $booted = false;

    public function boot(Module $module): void
    {
        // bootstraps diferentes serão ignorados
        // isso facilita a atribuição em massa
        if (! $module instanceof ConsoleModule || $this->isTerminalExecution() === false) {
            return;
        }

        $sourceSet = new RoutineSourceSet($module::class);

        // o dev irá adicionar os diretórios na implementação do módulo
        $module->bootRoutineDirectories($sourceSet);

        $this->sourceHandler()
            ->setScriptName($module->getScriptName())
            ->setScriptPath($module->getScriptPath())
            ->addSources($sourceSet);

        $this->booted = true;
    }

    public function isBooted(): bool
    {
        return $this->booted;
    }

    public function resolve(Input $input): ?ActionDescriptor
    {
        $scriptName = $this->sourceHandler()->getScriptName();
        $scriptPath = $this->sourceHandler()->getScriptPath();

        // talvez compartilhar o terminal via container
        $terminal = new Terminal($scriptPath);

        $terminal->setHowToUse("./$scriptName routine [options] [arguments]");

        foreach ($this->sourceHandler()->getDirectoryList() as $directory) {
            $terminal->loadRoutinesFrom($directory);
        }

        /** @var ConsoleDescriptor $actionDescriptor */
        $actionDescriptor = $this->sourceHandler()->getDescriptorTo($input);

        $module = $actionDescriptor->module();

        $moduleBootstrap = $this->moduleSet()->findByClass($module);

        if ($moduleBootstrap === null) {
            throw new RuntimeException(
                'At least one Routine Source Set must be added via addSources'
            );
        }

        $moduleBootstrap->bootDependencies($this->container());

        ob_start();

        /** @var array<int,string> $terminalArguments */
        $terminalArguments = $input->toArray();

        $terminal->run($terminalArguments);

        return $actionDescriptor->withOutput(
            (string)ob_get_clean(),
            $terminal->executedStatus()
        );
    }

    /** @return ConsoleSourceHandler */
    public function sourceHandler(): SourceHandler
    {
        if ($this->container()->has(ConsoleSourceHandler::class) === false) {
            $this->container()->addSingleton(
                ConsoleSourceHandler::class,
                fn() => new ConsoleSourceHandler()
            );
        }

        return $this->container()->get(ConsoleSourceHandler::class);
    }

    public function isTerminalExecution(): bool
    {
        return PHP_SAPI === 'cli';
    }
}
