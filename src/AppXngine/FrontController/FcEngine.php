<?php

declare(strict_types=1);

namespace Iquety\Application\AppEngine\FrontController;

use DomainException;
use Iquety\Application\AppEngine\Action\Input;
use Iquety\Application\AppEngine\Action\MethodNotAllowedException;
use Iquety\Application\Bootstrap;
use Iquety\Application\AppEngine\AppEngine;
use Iquety\Application\AppEngine\FrontController\Command\Command;
use Iquety\Application\AppEngine\FrontController\Command\NotFoundCommand;
use Iquety\Application\AppEngine\ModuleSet;
use Iquety\Application\Application;
use Iquety\Injection\InversionOfControl;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
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

        // para forçar a presença do container
        $this->container();

        $directorySet = new DirectorySet($bootstrap::class);

        // os diretórios são adicionados na implementação
        // do bootstrap do módulo
        $bootstrap->bootDirectories($directorySet);

        $this->sourceHandler()
            ->setErrorCommandClass($bootstrap->getErrorCommandClass())
            ->setNotFoundCommandClass($bootstrap->getNotFoundCommandClass())
            ->setMainCommandClass($bootstrap->getMainCommandClass())
            ->addSources($directorySet);
    }

    /** @param array<string,Bootstrap> $moduleList */
    public function execute(
        RequestInterface $request,
        ModuleSet $moduleSet,
        Application $application
    ): ?ResponseInterface {
        $descriptor = $this->sourceHandler()
            ->getDescriptorTo($request->getUri()->getPath());

        if ($descriptor === null) {
            return null;
        }

        $module = $descriptor->module();
        $action = $descriptor->action();
        $params = $descriptor->params();

        $this->container()->registerSingletonDependency(
            Input::class,
            fn() => new Input($params)
        );

        $control = new InversionOfControl($this->container());

        try {
            if ($module === 'main') {
                return $control->resolveTo(Command::class, $action);
            }

            $moduleBootstrap = $moduleSet->findByClass($module);

            if ($moduleBootstrap === null) {
                return $control->resolveTo(
                    Command::class,
                    $this->sourceHandler()->getNotFoundDescriptor([])->action()
                );
            }

            $moduleBootstrap->bootDependencies($application);

            return $control->resolveTo(Command::class, $action);
        } catch (Throwable $exception) {
            $this->container()->registerSingletonDependency(
                Throwable::class,
                fn() => $exception
            );

            return $control->resolveTo(
                Command::class,
                $this->sourceHandler()->getErrorDescriptor([])->action()
            );
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
