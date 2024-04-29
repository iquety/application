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
    private ?SourceHandler $handler = null;

    public function boot(Bootstrap $bootstrap): void
    {
        if (! $bootstrap instanceof FcBootstrap) {
            // TODO lançar exceção
            return;
        }

        // para verificar a existencia do container
        $this->container();

        $directorySet = new DirectorySet($bootstrap::class);

        $bootstrap->bootDirectories($directorySet);

        $this->sourceHandler()
            ->setErrorCommand($bootstrap->getErrorCommand())
            ->setNotFoundCommand($bootstrap->getNotFoundCommand())
            ->setRootCommand($bootstrap->getRootCommand())
            ->addSources($directorySet);
    }

    /** @param array<string,Bootstrap> $moduleList */
    public function execute(
        RequestInterface $request,
        array $moduleList,
        Closure $bootModuleDependencies
    ): ?ResponseInterface {
        $descriptor = $this->sourceHandler()
            ->getDescriptorTo($request->getUri()->getPath());

        if ($descriptor === null) {
            return null;
        }

        $control = new InversionOfControl($this->container());

        try {
            $module = $descriptor->module();
            $action = $descriptor->action();
            $params = $descriptor->params();

            $bootModuleDependencies($moduleList[$module]);

            $this->container()->registerSingletonDependency(
                Input::class,
                fn() => new Input($params)
            );

            try {
                return $control->resolveTo(Command::class, $action);
            } catch (MethodNotAllowedException) {
                return null;
            }
        } catch (Throwable $exception) {
            $this->container()->registerSingletonDependency(
                Throwable::class,
                fn() => $exception
            );

            // TODO: faz sentido?? o Application também tem erro.
            $descriptor = $this->sourceHandler()->getErrorDescriptor();

            return $control->resolveTo(Command::class, $descriptor->action());
        }
    }

    private function sourceHandler(): SourceHandler
    {
        if ($this->container()->has(SourceHandler::class) === false) {
            $this->container()->registerSingletonDependency(SourceHandler::class, SourceHandler::class);
        }

        if ($this->handler !== null) {
            return $this->handler;
        }

        $this->handler = $this->container()->get(SourceHandler::class);

        return $this->handler;
    }
}
