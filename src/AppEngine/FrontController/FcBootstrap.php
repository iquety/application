<?php

declare(strict_types=1);

namespace Iquety\Application\AppEngine\FrontController;

use Iquety\Application\AppEngine\Bootstrap;
use Iquety\Application\AppEngine\FrontController\Command\ErrorCommand;
use Iquety\Application\AppEngine\FrontController\Command\MainCommand;
use Iquety\Application\AppEngine\FrontController\Command\NotFoundCommand;
use Iquety\Injection\Container;

abstract class FcBootstrap implements Bootstrap
{
    abstract public function bootDependencies(Container $container): void;

    public function bootNamespaces(SourceSet &$sourceSet): void
    {
        // ...
    }

    public function getErrorCommandClass(): string
    {
        return ErrorCommand::class;
    }

    public function getNotFoundCommandClass(): string
    {
        return NotFoundCommand::class;
    }

    public function getMainCommandClass(): string
    {
        return MainCommand::class;
    }
}
