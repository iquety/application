<?php

declare(strict_types=1);

namespace Iquety\Application\IoEngine\Console;

use Iquety\Application\IoEngine\ActionDescriptor;
use Iquety\Application\IoEngine\IoEngine;
use Iquety\Application\IoEngine\Bootstrap;
use Iquety\Application\IoEngine\Action\Input;
use Iquety\Application\IoEngine\SourceHandler;
use Iquety\Console\Terminal;
use RuntimeException;

/** @SuppressWarnings(PHPMD.CouplingBetweenObjects) */
class ConsoleEngine extends IoEngine
{
    private bool $booted = false;

    public function boot(Bootstrap $bootstrap): void
    {
        // bootstraps diferentes serão ignorados
        // isso facilita a atribuição em massa
        if (! $bootstrap instanceof ConsoleBootstrap || PHP_SAPI !== 'cli') {
            return;
        }

        $sourceSet = new RoutineSourceSet($bootstrap::class);

        // o dev irá adicionar os diretórios na implementação do módulo
        $bootstrap->bootRoutineDirectories($sourceSet);

        $this->sourceHandler()
            ->setCommandName($bootstrap->getCommandName())
            ->setCommandPath($bootstrap->getCommandPath())
            ->addSources($sourceSet);

        $this->booted = true;
    }

    public function isBooted(): bool
    {
        return $this->booted;
    }

    public function resolve(Input $input): ?ActionDescriptor
    {
        $this->container()->addSingleton(Input::class, $input);

        $commandName = $this->sourceHandler()->getCommandName();
        $commandPath = $this->sourceHandler()->getCommandPath();

        // talvez compartilhar o terminal via container
        $terminal = new Terminal($commandPath);

        $terminal->setHowToUse("./$commandName routine [options] [arguments]");

        foreach($this->sourceHandler()->getDirectoryList() as $directory) {
            $terminal->loadRoutinesFrom($directory);
        }

        $actionDescriptor = $this->sourceHandler()->getDescriptorTo($input);

        $module = $actionDescriptor->module();

        $moduleBootstrap = $this->moduleSet()->findByClass($module);

        if ($moduleBootstrap === null) {
            throw new RuntimeException('At least one engine must be provided');
        }

        $moduleBootstrap->bootDependencies($this->container());

        ob_start();

        $terminal->run($input->toArray());

        return ConsoleDescriptor::factory($module, ob_get_clean(), $terminal->executedStatus());
    }

    /** @return ConsoleSourceHandler */
    public function sourceHandler(): SourceHandler
    {
        if ($this->container()->has(ConsoleSourceHandler::class) === false) {
            $this->container()->addSingleton(
                ConsoleSourceHandler::class,
                fn() => new ConsoleSourceHandler($this->container())
            );
        }

        return $this->container()->get(ConsoleSourceHandler::class);
    }
}
