<?php

declare(strict_types=1);

namespace Iquety\Application\AppEngine\Mvc;

use Iquety\Application\AppEngine\Bootstrap;
use Iquety\Application\AppEngine\Mvc\Controller\Controller;
use Iquety\Application\AppEngine\Mvc\Controller\ErrorController;
use Iquety\Application\AppEngine\Mvc\Controller\MainController;
use Iquety\Application\AppEngine\Mvc\Controller\NotFoundController;
use Iquety\Injection\Container;
use Iquety\Routing\Router;

abstract class MvcBootstrap implements Bootstrap
{
    abstract public function bootDependencies(Container $container): void;

    /** @SuppressWarnings(PHPMD.UnusedFormalParameter) */
    public function bootRoutes(Router &$router): void
    {
        //...
    }

    public function getActionType(): string
    {
        return Controller::class;
    }

    public function getErrorActionClass(): string
    {
        return ErrorController::class;
    }

    public function getMainActionClass(): string
    {
        return MainController::class;
    }

    public function getNotFoundActionClass(): string
    {
        return NotFoundController::class;
    }
}
