<?php

declare(strict_types=1);

namespace Iquety\Application\IoEngine\Console;

use Iquety\Application\IoEngine\Module;
use Iquety\Injection\Container;

abstract class ConsoleModule implements Module
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
        throw new NotImplementedException(
            'The ConsoleModule module does not have an implementation ' .
            'for this method, as it does not use Actions.'
        );
    }

    public function getErrorActionClass(): string
    {
        throw new NotImplementedException(
            'The ConsoleModule module does not have an implementation ' .
            'for this method, as it does not use Actions.'
        );
    }

    public function getNotFoundActionClass(): string
    {
        throw new NotImplementedException(
            'The ConsoleModule module does not have an implementation ' .
            'for this method, as it does not use Actions.'
        );
    }

    public function getMainActionClass(): string
    {
        throw new NotImplementedException(
            'The ConsoleModule module does not have an implementation ' .
            'for this method, as it does not use Actions.'
        );
    }
}
