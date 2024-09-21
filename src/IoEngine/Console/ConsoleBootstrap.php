<?php

declare(strict_types=1);

namespace Iquety\Application\IoEngine\Console;

use Iquety\Application\IoEngine\Bootstrap;
use Iquety\Injection\Container;

abstract class ConsoleBootstrap implements Bootstrap
{
    abstract public function bootDependencies(Container $container): void;

    /**
     * Devolve o nome do arquivo contendo o script de terminal
     * Ex.: app, artisan, etc
     */
    abstract public function getCommandName(): string;

    /** Devolve o diretório real da aplicação que implementa o Console */
    abstract public function getCommandPath(): string;

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @codeCoverageIgnore
     */
    public function bootRoutineDirectories(RoutineSourceSet &$sourceSet): void
    {
        //...
    }

    public function getActionType(): string
    {
        // O mecanismo Console não usa Actions

        return '';
    }

    public function getErrorActionClass(): string
    {
        // O mecanismo Console não usa Actions

        return '';
    }

    public function getNotFoundActionClass(): string
    {
        // O mecanismo Console não usa Actions

        return '';
    }

    public function getMainActionClass(): string
    {
        // O mecanismo Console não usa Actions

        return '';
    }
}
