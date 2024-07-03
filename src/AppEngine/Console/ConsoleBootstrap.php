<?php

declare(strict_types=1);

namespace Iquety\Application\AppEngine\Console;

use Iquety\Application\AppEngine\Bootstrap;
use Iquety\Console\Routine;
use Iquety\Console\Routines\Help;
use Iquety\Injection\Container;

abstract class ConsoleBootstrap implements Bootstrap
{
    abstract public function bootDependencies(Container $container): void;

    /** @SuppressWarnings(PHPMD.UnusedFormalParameter) */
    public function bootNamespaces(SourceSet &$sourceSet): void
    {
        // ...
    }

    public function getActionType(): string
    {
        return Routine::class;
    }

    public function getErrorActionClass(): string
    {
        return Help::class;
    }

    public function getNotFoundActionClass(): string
    {
        return Help::class;
    }

    public function getMainActionClass(): string
    {
        return Help::class;
    }
}
