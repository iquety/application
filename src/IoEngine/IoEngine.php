<?php

declare(strict_types=1);

namespace Iquety\Application\IoEngine;

use Iquety\Application\IoEngine\Action\ActionDescriptor;
use Iquety\Application\IoEngine\Action\Input;
use Iquety\Injection\Container;
use OutOfBoundsException;

abstract class IoEngine
{
    private ?Container $container = null;

    private ?ModuleSet $moduleSet = null;

    abstract public function boot(Module $module): void;

    /**
     * Tenta resolver a solicitação do usuário usando este mecanismo.
     * Se for retornada uma resposta, EngineSet irá enviá-la para o usuário,
     * se for retornado null, EngineSet irá solicitar ao próximo mecanismo.
     */
    abstract public function resolve(Input $input): ?ActionDescriptor;

    abstract public function sourceHandler(): SourceHandler;

    public function useContainer(Container $container): void
    {
        $this->container = $container;
    }

    public function useModuleSet(ModuleSet $moduleSet): void
    {
        $this->moduleSet = $moduleSet;
    }

    public function container(): Container
    {
        if ($this->container === null) {
            throw new OutOfBoundsException(
                'The container was not made available with the useContainer method'
            );
        }

        return $this->container;
    }

    public function moduleSet(): ModuleSet
    {
        if ($this->moduleSet === null) {
            throw new OutOfBoundsException(
                'The module set was not made available with the useModuleSet method'
            );
        }

        return $this->moduleSet;
    }
}
