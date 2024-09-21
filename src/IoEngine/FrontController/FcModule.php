<?php

declare(strict_types=1);

namespace Iquety\Application\IoEngine\FrontController;

use Iquety\Application\IoEngine\FrontController\Command\Command;
use Iquety\Application\IoEngine\FrontController\Command\ErrorCommand;
use Iquety\Application\IoEngine\FrontController\Command\MainCommand;
use Iquety\Application\IoEngine\FrontController\Command\NotFoundCommand;
use Iquety\Application\IoEngine\Module;
use Iquety\Injection\Container;

abstract class FcModule implements Module
{
    abstract public function bootDependencies(Container $container): void;

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @codeCoverageIgnore
     */
    public function bootNamespaces(CommandSourceSet &$sourceSet): void
    {
        // ...
    }

    public function getActionType(): string
    {
        return Command::class;
    }

    public function getErrorActionClass(): string
    {
        return ErrorCommand::class;
    }

    public function getNotFoundActionClass(): string
    {
        return NotFoundCommand::class;
    }

    public function getMainActionClass(): string
    {
        return MainCommand::class;
    }
}
