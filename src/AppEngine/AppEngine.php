<?php

declare(strict_types=1);

namespace Iquety\Application\AppEngine;

use Closure;
use Iquety\Application\Application;
use Iquety\Application\Bootstrap;
use Iquety\Application\Http\HttpFactory;
use Iquety\Application\Http\HttpResponseFactory;
use Iquety\Injection\Container;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

abstract class AppEngine
{
    public function __construct(private Container $container)
    {
    }

    protected function container(): Container
    {
        return $this->container;
    }

    protected function responseFactory(): HttpResponseFactory
    {
        /** @var HttpResponseFactory */
        $factory = $this->container()->get(HttpResponseFactory::class);

        return $factory;
    }

    abstract public function boot(Bootstrap $bootstrap): void;

    abstract public function execute(
        RequestInterface $request,
        array $moduleList,
        Closure $bootModuleDependencies
    ): ?ResponseInterface;
}
