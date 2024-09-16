<?php

declare(strict_types=1);

namespace Iquety\Application\AppEngine\FrontController;

use Iquety\Application\AppEngine\Bootstrap;
use Iquety\Application\AppEngine\FrontController\Command\Command;
use Iquety\Application\AppEngine\FrontController\Command\ErrorCommand;
use Iquety\Application\AppEngine\FrontController\Command\MainCommand;
use Iquety\Application\AppEngine\FrontController\Command\NotFoundCommand;
use Iquety\Injection\Container;

abstract class FcBootstrap implements Bootstrap
{
    abstract public function bootDependencies(Container $container): void;

    /** @SuppressWarnings(PHPMD.UnusedFormalParameter) */
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
