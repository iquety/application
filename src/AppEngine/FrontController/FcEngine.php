<?php

declare(strict_types=1);

namespace Iquety\Application\AppEngine\FrontController;

use Closure;
use Iquety\Application\AppEngine\Action\Input;
use Iquety\Application\AppEngine\Action\MethodNotAllowedException;
use Iquety\Application\Bootstrap;
use Iquety\Application\AppEngine\AppEngine;
use Iquety\Injection\InversionOfControl;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;
use Throwable;

/** @SuppressWarnings(PHPMD.CouplingBetweenObjects) */
class FcEngine extends AppEngine
{
    private ?CommandHandler $handler = null;

    private DirectorySet $directorySet;

    public function boot(Bootstrap $bootstrap): void
    {
        if (! $bootstrap instanceof FcBootstrap) {
            // TODO lançar exceção
            return;
        }

        $this->directorySet = new DirectorySet();

        $bootstrap->bootDirectories($this->directorySet);

        $this->handler()->addModuleSources($bootstrap::class, $this->directorySet);
    }

    /** @param array<string,Bootstrap> $moduleList */
    public function execute(
        RequestInterface $request,
        array $moduleList,
        Closure $bootDependencies
    ): ?ResponseInterface {
        $handler = $this->handler();

        if ($handler->commandSources() === []) {
            throw new RuntimeException(
                'No directories registered as command source'
            );
        }

        // $possibiliyList = $handler->process($request->getUri()->getPath());

        $command = $handler->resolveCommand($request->getUri()->getPath());

        if ($command === null) {
            return null;
        }

        try {
            $module = $command->module();
            $action = $command->action();
            $params = $command->params();

            $bootDependencies($moduleList[$module]);

            $this->container()->registerSingletonDependency(
                Input::class,
                fn() => new Input($params)
            );

            $control = new InversionOfControl($this->container());

            try {
                return $control->resolveTo(Command::class, $action);
            } catch (MethodNotAllowedException) {
                return null;
            }
        } catch (Throwable $exception) {
            return $this->responseFactory()->serverErrorResponse($exception);
        }
    }

    public function getDirectorySet(): DirectorySet
    {
        return $this->directorySet;
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
