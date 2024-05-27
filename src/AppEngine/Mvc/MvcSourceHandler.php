<?php

declare(strict_types=1);

namespace Iquety\Application\AppEngine\Mvc;

use InvalidArgumentException;
use Iquety\Application\AppEngine\ActionDescriptor;
use Iquety\Application\AppEngine\Action\Input;
use Iquety\Application\AppEngine\Mvc\Controller\Controller;
use Iquety\Application\AppEngine\Mvc\Controller\ErrorController;
use Iquety\Application\AppEngine\Mvc\Controller\MainController;
use Iquety\Application\AppEngine\Mvc\Controller\NotFoundController;
use Iquety\Application\AppEngine\SourceHandler;
use Iquety\Application\AppEngine\UriParser;
use Iquety\Routing\Route;
use Iquety\Routing\Router;
use RuntimeException;

class MvcSourceHandler implements SourceHandler
{
    private string $errorControllerClass = ErrorController::class;

    private string $mainControllerClass = MainController::class;

    private string $notFoundControllerClass = NotFoundController::class;

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
        if ($this->hasRoutes() === false) {
            throw new RuntimeException(
                'There are no registered routes'
            );
        }

        if ($input->getPath() === []) {
            return $this->getMainDescriptor($input);
        }

        $this->router->process($input->getMethod(), $input->getPathString());

        if ($this->router->routeNotFound() === true) {
            return null;
        }

        /** @var Route $route */
        $route = $this->router->currentRoute();

        $bootstrapClass = $route->module();
        $className = $route->action();
        $classMethod = $route->actionMethod();
        $paramList = $route->params();

        foreach($paramList as $name => $value) {
            $paramList[$name] = (new UriParser(''))->fixTypes($value);
        }

        $input->appendParams($paramList);

        if ($className === '') {
            throw new RuntimeException('The route found does not have a action');
        }

        return new ActionDescriptor(
            Controller::class,
            $bootstrapClass,
            $className,
            $classMethod
        );
    }

    public function getErrorDescriptor(): ActionDescriptor
    {
        return $this->makeDescriptor('error', $this->errorControllerClass, 'execute');
    }

    public function getMainDescriptor(): ActionDescriptor
    {
        return $this->makeDescriptor('main', $this->mainControllerClass, 'execute');
    }

    public function getNotFoundDescriptor(): ActionDescriptor
    {
        return $this->makeDescriptor('not-found', $this->notFoundControllerClass, 'execute');
    }

    public function setErrorActionClass(string $actionClass): self
    {
        $this->assertController($actionClass);

        $this->errorControllerClass = $actionClass;

        return $this;
    }

    public function setMainActionClass(string $actionClass): self
    {
        $this->assertController($actionClass);

        $this->mainControllerClass = $actionClass;

        return $this;
    }

    public function setNotFoundActionClass(string $actionClass): self
    {
        $this->assertController($actionClass);
        
        $this->notFoundControllerClass = $actionClass;

        return $this;
    }

    private function assertController(string $actionClass): void
    {
        if (is_subclass_of($actionClass, Controller::class) === false) {
            throw new InvalidArgumentException("Class $actionClass is not a valid controller");
        }
    }

    private function makeDescriptor(
        string $bootstrapClass,
        string $className,
        $actionName
    ): ActionDescriptor {
        return new ActionDescriptor(
            Controller::class,
            $bootstrapClass,
            $className,
            'execute'
        );
    }
}
