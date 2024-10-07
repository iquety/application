<?php

declare(strict_types=1);

namespace Iquety\Application\IoEngine\FrontController;

use Iquety\Application\IoEngine\Action\ActionDescriptor;
use Iquety\Application\IoEngine\IoEngine;
use Iquety\Application\IoEngine\Action\Input;
use Iquety\Application\IoEngine\Module;
use Iquety\Application\IoEngine\SourceHandler;
use RuntimeException;

/** @SuppressWarnings(PHPMD.CouplingBetweenObjects) */
class FcEngine extends IoEngine
{
    private bool $booted = false;

    public function boot(Module $module): void
    {
        // módulos diferentes serão ignorados
        // isso facilita a atribuição em massa
        if (! $module instanceof FcModule) {
            return;
        }

        $sourceSet = new CommandSourceSet($module::class);

        // o dev irá adicionar os diretórios na implementação do módulo
        $module->bootNamespaces($sourceSet);

        $this->sourceHandler()
            ->setErrorActionClass($module->getErrorActionClass())
            ->setMainActionClass($module->getMainActionClass())
            ->setNotFoundActionClass($module->getNotFoundActionClass())
            ->addSources($sourceSet);

        $this->booted = true;
    }

    public function isBooted(): bool
    {
        return $this->booted;
    }

    public function resolve(Input $input): ?ActionDescriptor
    {
        $actionDescriptor = $this->sourceHandler()->getDescriptorTo($input);

        if ($actionDescriptor === null) {
            // o descritor NotFound será definido pelo EngineSet
            return null;
        }

        $module = $actionDescriptor->module();

        if ($module === 'main') {
            return $actionDescriptor;
        }

        $moduleBootstrap = $this->moduleSet()->findByClass($module);

        if ($moduleBootstrap === null) {
            throw new RuntimeException('At least one module must be provided');
        }

        $moduleBootstrap->bootDependencies($this->container());

        return $actionDescriptor;
    }

    /** @return FcSourceHandler */
    public function sourceHandler(): SourceHandler
    {
        if ($this->container()->has(FcSourceHandler::class) === false) {
            $this->container()->addSingleton(FcSourceHandler::class);
        }

        return $this->container()->get(FcSourceHandler::class);
    }
}
