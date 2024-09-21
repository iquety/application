<?php

declare(strict_types=1);

namespace Iquety\Application\IoEngine\Mvc;

use Iquety\Application\IoEngine\Module;
use Iquety\Application\IoEngine\Mvc\Controller\Controller;
use Iquety\Application\IoEngine\Mvc\Controller\ErrorController;
use Iquety\Application\IoEngine\Mvc\Controller\MainController;
use Iquety\Application\IoEngine\Mvc\Controller\NotFoundController;
use Iquety\Injection\Container;
use Iquety\Routing\Router;

abstract class MvcModule implements Module
{
    abstract public function bootDependencies(Container $container): void;

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @codeCoverageIgnore
     */
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
