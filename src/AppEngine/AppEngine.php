<?php

declare(strict_types=1);

namespace Iquety\Application\AppEngine;

use Iquety\Application\Container;
use Iquety\Application\Http\HttpResponseFactory;
use OutOfBoundsException;

abstract class AppEngine
{
    private ?Container $container = null;

    public function useContainer(Container $container): void
    {
        $this->container = $container;
    }

    protected function container(): Container
    {
        if ($this->container === null) {
            throw new OutOfBoundsException(
                'The container was not made available with the useContainer method'
            );
        }

        return $this->container;
    }

    protected function responseFactory(): HttpResponseFactory
    {
        return $this->container()->get(HttpResponseFactory::class);
    }

    abstract public function boot(Bootstrap $bootstrap): void;

    /** @param array<string,Bootstrap> $moduleList */
    abstract public function resolve(
        Input $input,
        ModuleSet $moduleSet,
    ): ResponseDescriptor;
}
