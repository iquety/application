<?php

declare(strict_types=1);

namespace Iquety\Application\IoEngine\Mvc;

use Iquety\Application\IoEngine\Action\ActionDescriptor;
use Iquety\Application\IoEngine\IoEngine;
use Iquety\Application\IoEngine\Action\Input;
use Iquety\Application\IoEngine\Module;
use Iquety\Application\IoEngine\SourceHandler;
use Iquety\Routing\Router;
use RuntimeException;

/** @SuppressWarnings(PHPMD.CouplingBetweenObjects) */
class MvcEngine extends IoEngine
{
    private ?Router $router = null;

    private bool $booted = false;

    public function boot(Module $module): void
    {
        // módulos diferentes serão ignorados
        // isso facilita a atribuição em massa
        if (! $module instanceof MvcModule) {
            return;
        }

        $router = $this->router();

        $router->forModule($module::class);

        // o dev irá adicionar rotas na implementação do módulo
        $module->bootRoutes($router);

        $this->sourceHandler()
            ->setErrorActionClass($module->getErrorActionClass())
            ->setMainActionClass($module->getMainActionClass())
            ->setNotFoundActionClass($module->getNotFoundActionClass())
            ->addRouter($router);

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

    private function router(): Router
    {
        if ($this->container()->has(Router::class) === false) {
            $this->container()->addSingleton(Router::class);
        }

        if ($this->router !== null) {
            return $this->router;
        }

        $this->router = $this->container()->get(Router::class);
        $this->router->resetModuleInfo();

        return $this->router;
    }

    /** @return MvcSourceHandler */
    public function sourceHandler(): SourceHandler
    {
        if ($this->container()->has(MvcSourceHandler::class) === false) {
            $this->container()->addSingleton(MvcSourceHandler::class);
        }

        return $this->container()->get(MvcSourceHandler::class);
    }
}
