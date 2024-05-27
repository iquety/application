<?php

declare(strict_types=1);

namespace Iquety\Application\AppEngine\FrontController;

use Iquety\Application\AppEngine\ActionDescriptor;
use Iquety\Application\AppEngine\AppEngine;
use Iquety\Application\AppEngine\Bootstrap;
use Iquety\Application\AppEngine\Action\Input;
use Iquety\Application\AppEngine\SourceHandler;
use RuntimeException;

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

        $sourceSet = new SourceSet($bootstrap::class);

        // o dev irá adicionar os diretórios na implementação do módulo
        $bootstrap->bootNamespaces($sourceSet);

        $this->sourceHandler()
            ->setErrorActionClass($bootstrap->getErrorActionClass())
            ->setMainActionClass($bootstrap->getMainActionClass())
            ->setNotFoundActionClass($bootstrap->getNotFoundActionClass())
            ->addSources($sourceSet);
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

    /** @return FcSourceHandler */
    public function sourceHandler(): SourceHandler
    {
        if ($this->container()->has(FcSourceHandler::class) === false) {
            $this->container()->addSingleton(FcSourceHandler::class);
        }

        return $this->container()->get(FcSourceHandler::class);
    }
}
