<?php

declare(strict_types=1);

namespace Iquety\Application\AppEngine;

use Closure;
use Iquety\Application\Bootstrap;
use Iquety\Application\Http\HttpResponseFactory;
use Iquety\Injection\Container;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

abstract class AppEngine
{
    private ?Container $container = null;

    public function useContainer(Container $container): void
    {
        $this->container = $container;
    }
    
    protected function container(): Container
    {
        return $this->container;
    }

    protected function responseFactory(): HttpResponseFactory
    {
        return $this->container()->get(HttpResponseFactory::class);
    }

    abstract public function boot(Bootstrap $bootstrap): void;

    abstract public function execute(
        RequestInterface $request,
        array $moduleList,
        Closure $bootModuleDependencies
    ): ?ResponseInterface;
}
