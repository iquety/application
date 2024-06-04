<?php

declare(strict_types=1);

namespace Iquety\Application\AppEngine\Mvc;

use Iquety\Application\AppEngine\ActionDescriptor;
use Iquety\Application\AppEngine\AppEngine;
use Iquety\Application\AppEngine\Bootstrap;
use Iquety\Application\AppEngine\Action\Input;
use Iquety\Application\AppEngine\SourceHandler;
use Iquety\Routing\Router;
use RuntimeException;

/** @SuppressWarnings(PHPMD.CouplingBetweenObjects) */
class MvcEngine extends AppEngine
{
    private ?Router $router = null;

    public function boot(Bootstrap $bootstrap): void
    {
        // bootstraps diferentes serão ignorados
        // isso facilita a atribuição em massa
        if (! $bootstrap instanceof MvcBootstrap) {
            return;
        }

        $router = $this->router();

        $router->forModule($bootstrap::class);

        // o dev irá adicionar rotas na implementação do módulo
        $bootstrap->bootRoutes($router);

        $this->sourceHandler()
            ->setErrorActionClass($bootstrap->getErrorActionClass())
            ->setMainActionClass($bootstrap->getMainActionClass())
            ->setNotFoundActionClass($bootstrap->getNotFoundActionClass())
            ->addRouter($router);
    }

    public function resolve(Input $input): ?ActionDescriptor
    {
        $this->container()->addSingleton(Input::class, $input);

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
            throw new RuntimeException('At least one engine must be provided');
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
