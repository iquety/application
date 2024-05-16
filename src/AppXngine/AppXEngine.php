<?php

declare(strict_types=1);

namespace Iquety\Application\AppEngine;

use Closure;
use Iquety\Application\Application;
use Iquety\Application\Bootstrap;
use Iquety\Application\Http\HttpResponseFactory;
use Iquety\Injection\Container;
use OutOfBoundsException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

abstract class AppXEngine
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
    abstract public function execute(
        RequestInterface $request,
        ModuleSet $moduleSet,
        Application $application
    ): ?ResponseInterface;
}
