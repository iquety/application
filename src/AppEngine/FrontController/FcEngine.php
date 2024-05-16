<?php

declare(strict_types=1);

namespace Iquety\Application\AppEngine\FrontController;

use Iquety\Application\AppEngine\AppEngine;
use Iquety\Application\AppEngine\Bootstrap;
use Iquety\Application\AppEngine\FrontController\Command\Command;
use Iquety\Application\AppEngine\FrontController\Command\CommandDescriptor;
use Iquety\Application\AppEngine\FrontController\Command\ErrorCommand;
use Iquety\Application\AppEngine\FrontController\Command\MainCommand;
use Iquety\Application\AppEngine\FrontController\Command\NotFoundCommand;
use Iquety\Application\AppEngine\Input;
use Iquety\Application\AppEngine\ModuleSet;
use Iquety\Application\AppEngine\ResponseDescriptor;
use Iquety\Injection\InversionOfControl;
use RuntimeException;
use Throwable;

/** @SuppressWarnings(PHPMD.CouplingBetweenObjects) */
class FcEngine extends AppEngine
{
    public function boot(Bootstrap $bootstrap): void
    {
        // bootstraps diferentes serão ignorados
        // isso facilita a atribuição em massa
        if (! $bootstrap instanceof FcBootstrap) {
            return;
        }

        $this->moduleSet()->add($bootstrap);
        
        $directorySet = new DirectorySet($bootstrap::class);

        // o dev irá adicionar os diretórios na implementação do módulo
        $bootstrap->bootDirectories($directorySet);

        // $this->container()->addFactory(
        //     ErrorCommand::class,
        //     fn() => $bootstrap->getErrorCommandClass()
        // );

        // $this->container()->addFactory(
        //     NotFoundCommand::class,
        //     fn() => $bootstrap->getNotFoundCommandClass()
        // );

        // $this->container()->addFactory(
        //     MainCommand::class,
        //     fn() => $bootstrap->getMainCommandClass()
        // );

        $this->sourceHandler()
            ->addSources($directorySet);
    }

    public function resolve(Input $input): ?ResponseDescriptor
    {
        $this->container()->addSingleton(Input::class, fn() => $input);

        $commandDescriptor = $this->sourceHandler()->getDescriptorTo($input);

        if ($commandDescriptor === null) {
            // resposta será definida pelo EngineSet
            return null;
        }

        $module = $commandDescriptor->module();
        $action = $commandDescriptor->action();

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

            $moduleBootstrap->bootDependencies($this->container());

            return $control->resolveTo(Command::class, $action);
        } catch (Throwable $exception) {
            $this->container()->addSingleton(
                Throwable::class,
                fn() => $exception
            );

            return $control->resolveTo(
                Command::class,
                $this->sourceHandler()->getErrorDescriptor([])->action()
            );
        }
    }

    public function makeCommandDescriptor(Input $input): ?CommandDescriptor
    {
        if ($this->sourceHandler()->hasSources() === false) {
            throw new RuntimeException(
                'No directories registered as command source'
            );
        }

        // $uriData = parse_url($uri);
        // $path    = trim(trim($uriData['path']), '/');
        // $params  = $this->parseQuery($uriData['query'] ?? '');

        // if ($input->getPath() === '') {
        //     return new CommandDescriptor('main', $this->mainCommandClass, $input);
        // }

        // foreach ($this->getSourceList() as $directorySet) {
        //     $descriptor = $directorySet->getDescriptorTo($path);

        //     if ($descriptor !== null) {
        //         return $descriptor;
        //     }
        // }

        // var_dump($uriData['path'], explode('/', $uriData['path']));
        // exit;
        
        // $params = array_merge(explode('/', $uriData['path']), $params);

        // return $this->getNotFoundDescriptor($params);
    }

    public function sourceHandler(): SourceHandler
    {
        if ($this->container()->has(SourceHandler::class) === false) {
            $this->container()->addSingleton(SourceHandler::class);
        }

        return $this->container()->get(SourceHandler::class);
    }
}
