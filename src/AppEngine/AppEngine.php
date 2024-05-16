<?php

declare(strict_types=1);

namespace Iquety\Application\AppEngine;

use Iquety\Injection\Container;
use OutOfBoundsException;

abstract class AppEngine
{
    private ?Container $container = null;

    private ?ModuleSet $moduleSet = null;

    abstract public function boot(Bootstrap $bootstrap): void;

    protected function container(): Container
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

    /** 
     * Tenta resolver a solicitação do usuário usando este mecanismo.
     * Se for retornada uma resposta, EngineSet irá enviá-la para o usuário,
     * se for retornado null, EngineSet irá solicitar ao próximo mecanismo.
     */
    abstract public function resolve(Input $input): ?ResponseDescriptor;

    public function useContainer(Container $container): void
    {
        $this->container = $container;
    }

    public function useModuleSet(ModuleSet $moduleSet): void
    {
        $this->moduleSet = $moduleSet;
    }

    // protected function responseFactory(): HttpResponseFactory
    // {
    //     return $this->container()->get(HttpResponseFactory::class);
    // }
}
