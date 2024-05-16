<?php

declare(strict_types=1);

namespace Iquety\Application\AppEngine\Mvc;

use Iquety\Application\AppEngine\Bootstrap;
use Iquety\Injection\Container;
use Iquety\Routing\Router;

abstract class MvcBootstrap implements Bootstrap
{
    abstract public function bootDependencies(Container $container): void;

    public function bootRoutes(Router &$router): void
    {
        //...
    }

    public function getErrorControllerClass(): string
    {
        return ErrorController::class;
    }

    public function getNotFoundControllerClass(): string
    {
        return NotFoundController::class;
    }
}
