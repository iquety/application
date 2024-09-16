<?php

declare(strict_types=1);

namespace Iquety\Application\AppEngine\Console;

use Iquety\Application\AppEngine\ActionDescriptor;
use Iquety\Application\AppEngine\AppEngine;
use Iquety\Application\AppEngine\Bootstrap;
use Iquety\Application\AppEngine\Action\Input;
use Iquety\Application\AppEngine\SourceHandler;
use Iquety\Console\Terminal;
use RuntimeException;
use Throwable;

/** @SuppressWarnings(PHPMD.CouplingBetweenObjects) */
class ConsoleEngine extends AppEngine
{
    public function boot(Bootstrap $bootstrap): void
    {
        // bootstraps diferentes serão ignorados
        // isso facilita a atribuição em massa
        if (! $bootstrap instanceof ConsoleBootstrap) {
            return;
        }

        $sourceSet = new RoutineSourceSet($bootstrap::class);

        // o dev irá adicionar os diretórios na implementação do módulo
        $bootstrap->bootRoutineDirectories($sourceSet);

        $this->sourceHandler()
            ->setCommandName($bootstrap->getCommandName())
            ->setCommandPath($bootstrap->getCommandPath())
            ->addSources($sourceSet);
    }

    public function resolve(Input $input): ?ActionDescriptor
    {
        $this->container()->addSingleton(Input::class, $input);

        $commandName = $this->sourceHandler()->getCommandName();
        $commandPath = $this->sourceHandler()->getCommandPath();

        $terminal = new Terminal($commandPath);

        $terminal->setHowToUse("./$commandName routine [options] [arguments]");

        foreach($this->sourceHandler()->getDirectoryList() as $directory) {
            $terminal->loadRoutinesFrom($directory);
        }

        $actionDescriptor = $this->sourceHandler()->getDescriptorTo($input);

        if ($actionDescriptor === null) {
            // @see https://www.cyberciti.biz/faq/linux-bash-exit-status-set-exit-statusin-bash/
            return ConsoleDescriptor::factory('', 'Script not found', 127);
        }

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
            $this->container()->addSingleton(ConsoleSourceHandler::class);
        }

        return $this->container()->get(ConsoleSourceHandler::class);
    }
}
