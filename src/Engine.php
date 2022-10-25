<?php

declare(strict_types=1);

namespace Freep\Application;

use Freep\Application\Container\Container;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

abstract class Engine
{
    public function __construct(private Application $application)
    {
    }

    public function app(): Application
    {
        return $this->application;
    }

    abstract public function boot(string $moduleIdentifier, Bootstrap $bootstrap): void;

    abstract public function execute(array $moduleList, RequestInterface $request): ?ResponseInterface;
}
