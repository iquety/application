<?php

declare(strict_types=1);

namespace Iquety\Application\AppEngine\FrontController;

use Closure;
use Iquety\Application\Bootstrap;
use InvalidArgumentException;
use Iquety\Application\AppEngine\AppEngine;
use Iquety\Application\Http\HttpResponseFactory;
use Iquety\Injection\InversionOfControl;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;
use Throwable;

class FcEngine extends AppEngine
{
    private ?CommandHandler $handler = null;

    public function boot(Bootstrap $bootstrap): void
    {
        if (! $bootstrap instanceof FcBootstrap) {
            throw new InvalidArgumentException(
                sprintf('Invalid bootstrap. Required a %s', FcBootstrap::class)
            );
        }

        $moduleIdentifier = $bootstrap::class;
        $namespace = $bootstrap->commandsDirectory();

        $searchBar = strrpos($bootstrap::class, '\\');

        if ($searchBar !== false) {
            $lastBar = (int)$searchBar + 1;
            $namespace = substr($bootstrap::class, 0, $lastBar) . $bootstrap->commandsDirectory();
        }

        $this->handler()->addNamespace($moduleIdentifier, $namespace);
    }

    public function execute(
        RequestInterface $request,
        array $moduleList,
        Closure $bootModuleDependencies
    ): ?ResponseInterface
    {
        $handler = $this->handler();

        if ($handler->namespaces() === []) {
            throw new RuntimeException(
                'This bootstrap has no directories registered as command source'
            );
        }

        $handler->process($request->getMethod(), $request->getUri()->getPath());

        if ($handler->commandNotFound()) {
            return null;
        }

        
            $module = $handler->module();
            $action = $handler->action();
            $params = $handler->params();

            $bootModuleDependencies($moduleList[$module]);

            $control = new InversionOfControl($this->container());

            // TODO
            // Impedir o uso de classes que não implementem Command
            return $control->resolve($action, $params);
        
    }

    private function handler(): CommandHandler
    {
        if ($this->container()->has(CommandHandler::class) === false) {
            $this->container()->registerSingletonDependency(CommandHandler::class, CommandHandler::class);
        }

        if ($this->handler !== null) {
            return $this->handler;
        }

        $this->handler = $this->container()->get(CommandHandler::class);

        return $this->handler;
    }
}
