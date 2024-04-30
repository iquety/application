<?php

declare(strict_types=1);

namespace Iquety\Application\AppEngine\FrontController;

use Iquety\Application\AppEngine\FrontController\Command\ErrorCommand;
use Iquety\Application\AppEngine\FrontController\Command\MainCommand;
use Iquety\Application\AppEngine\FrontController\Command\NotFoundCommand;
use Iquety\Application\Application;
use Iquety\Application\Bootstrap;

abstract class FcBootstrap implements Bootstrap
{
    public function bootDirectories(DirectorySet &$directorySet): void
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

    abstract public function bootDependencies(Application $app): void;
}
