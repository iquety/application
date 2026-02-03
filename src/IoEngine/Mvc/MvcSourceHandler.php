<?php

declare(strict_types=1);

namespace Iquety\Application\IoEngine\Mvc;

use Closure;
use InvalidArgumentException;
use Iquety\Application\IoEngine\Action\ActionDescriptor;
use Iquety\Application\IoEngine\Action\Input;
use Iquety\Application\IoEngine\Mvc\Controller\Controller;
use Iquety\Application\IoEngine\Mvc\Controller\ErrorController;
use Iquety\Application\IoEngine\Mvc\Controller\MainController;
use Iquety\Application\IoEngine\Mvc\Controller\NotFoundController;
use Iquety\Application\IoEngine\SourceHandler;
use Iquety\Application\IoEngine\ValueParser;
use Iquety\Routing\Route;
use Iquety\Routing\Router;
use RuntimeException;

class MvcSourceHandler implements SourceHandler
{
    private string $errorCtrlClass = ErrorController::class;

    private string $mainCtrlClass = MainController::class;

    private string $notFoundCtrlClass = NotFoundController::class;

    private ?Router $router = null;

    public function addRouter(Router $router): void
    {
        $this->router = $router;
    }

    public function hasRoutes(): bool
    {
        return $this->router !== null && $this->router->routes() !== [];
    }

    public function getDescriptorTo(Input $input): ?ActionDescriptor
    {
        if ($this->router === null) {
            throw new RuntimeException(
                'No router specified'
            );
        }

        if ($input->getPathString() !== '' && $this->hasRoutes() === false) {
            throw new RuntimeException(
                'There are no registered routes'
            );
        }

        $this->router->process($input->getMethod(), $input->getPathString());

        if ($this->router->routeNotFound() === true && $input->getPath() === []) {
            return $this->getMainDescriptor();
        }

        if ($this->router->routeNotFound() === true) {
            return null;
        }

        /** @var Route $route */
        $route = $this->router->currentRoute();

        $moduleClass = $route->module();
        $className = $route->action();
        $classMethod = $route->actionMethod();
        $paramList = $route->params();

        foreach ($paramList as $name => $value) {
            $paramList[$name] = (new ValueParser($value))->withCorrectType();
        }

        $input->appendParams($paramList);

        if ($className === '') {
            throw new RuntimeException('The route found does not have a action');
        }

        return new ActionDescriptor(
            Controller::class,
            $moduleClass,
            $className,
            $classMethod
        );
    }

    public function getErrorDescriptor(): ActionDescriptor
    {
        return $this->makeDescriptor('error', $this->errorCtrlClass, 'execute');
    }

    public function getMainDescriptor(): ActionDescriptor
    {
        return $this->makeDescriptor('main', $this->mainCtrlClass, 'execute');
    }

    public function getNotFoundDescriptor(): ActionDescriptor
    {
        return $this->makeDescriptor('not-found', $this->notFoundCtrlClass, 'execute');
    }

    public function setErrorActionClass(string $actionClass): self
    {
        $this->assertController($actionClass);

        $this->errorCtrlClass = $actionClass;

        return $this;
    }

    public function setMainActionClass(string $actionClass): self
    {
        $this->assertController($actionClass);

        $this->mainCtrlClass = $actionClass;

        return $this;
    }

    public function setNotFoundActionClass(string $actionClass): self
    {
        $this->assertController($actionClass);

        $this->notFoundCtrlClass = $actionClass;

        return $this;
    }

    private function assertController(string $actionClass): void
    {
        if (is_subclass_of($actionClass, Controller::class) === false) {
            throw new InvalidArgumentException("Class $actionClass is not a valid controller");
        }
    }

    private function makeDescriptor(
        string $moduleClass,
        Closure|string $className,
        string $actionName
    ): ActionDescriptor {
        return new ActionDescriptor(
            Controller::class,
            $moduleClass,
            $className,
            $actionName
        );
    }
}
