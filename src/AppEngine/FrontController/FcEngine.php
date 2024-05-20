<?php

declare(strict_types=1);

namespace Iquety\Application\AppEngine\FrontController;

use Iquety\Application\AppEngine\AppEngine;
use Iquety\Application\AppEngine\Bootstrap;
use Iquety\Application\AppEngine\FrontController\Command\Command;
use Iquety\Application\AppEngine\FrontController\Command\CommandDescriptor;
use Iquety\Application\AppEngine\Input;
use Iquety\Application\AppEngine\ModuleSet;
use Iquety\Application\AppEngine\ResponseDescriptor;
use Iquety\Injection\InversionOfControl;
use RuntimeException;
use Throwable;

/** @SuppressWarnings(PHPMD.CouplingBetweenObjects) */
class FcEngine extends AppEngine
{
    public function boot(Bootstrap $bootstrap): void
    {
        // bootstraps diferentes serão ignorados
        // isso facilita a atribuição em massa
        if (! $bootstrap instanceof FcBootstrap) {
            return;
        }

        $this->moduleSet()->add($bootstrap);

        $directorySet = new DirectorySet($bootstrap::class);

        // o dev irá adicionar os diretórios na implementação do módulo
        $bootstrap->bootDirectories($directorySet);

        $this->sourceHandler()
            ->setErrorCommandClass($bootstrap->getErrorCommandClass())
            ->setMainCommandClass($bootstrap->getMainCommandClass())
            ->setNotFoundCommandClass($bootstrap->getNotFoundCommandClass())
            ->addSources($directorySet);
    }

    public function resolve(Input $input): ?CommandDescriptor
    {
        // try {
            if ($this->sourceHandler()->hasSources() === false) {
                throw new RuntimeException(
                    'No directories registered as command source'
                );
            }

            $this->container()->addSingleton(Input::class, $input);
        
            $commandDescriptor = $this->sourceHandler()->getDescriptorTo($input);

            if ($commandDescriptor === null) {
                // resposta será definida pelo EngineSet
                return null;
            }

            $module = $commandDescriptor->module();
            // $action = $commandDescriptor->action();

            // $control = new InversionOfControl($this->container());
        
            if ($module === 'main') {
                return $commandDescriptor;
                // return $control->resolveTo(Command::class, $action);
            }

            $moduleBootstrap = $this->moduleSet()->findByClass($module);

            if ($moduleBootstrap === null) {
                return $this->sourceHandler()->getNotFoundDescriptor($input)->action();

                // return $control->resolveTo(Command::class, $action);
            }

            $moduleBootstrap->bootDependencies($this->container());

            return $commandDescriptor;
            // return $control->resolveTo(Command::class, $action);
        // } catch (Throwable $exception) {
        //     $this->container()->addSingleton(Throwable::class, $exception);

        //     return $this->sourceHandler()->getErrorDescriptor($input);

        //     // return $control->resolveTo(Command::class, $action);
        // }
    }

    public function sourceHandler(): SourceHandler
    {
        if ($this->container()->has(SourceHandler::class) === false) {
            $this->container()->addSingleton(SourceHandler::class);
        }

        return $this->container()->get(SourceHandler::class);
    }
}
