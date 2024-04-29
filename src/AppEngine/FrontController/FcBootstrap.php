<?php

declare(strict_types=1);

namespace Iquety\Application\AppEngine\FrontController;

use Directory;
use Iquety\Application\Application;
use Iquety\Application\Bootstrap;
use Iquety\Application\Settings;

abstract class FcBootstrap implements Bootstrap
{
    public function bootDirectories(DirectorySet &$directories): void
    {
        // ...
    }

    public function getErrorCommand(): string
    {
        return ErrorCommand::class;
    }

    public function getNotFoundCommand(): string
    {
        return NotFoundCommand::class;
    }

    public function getRootCommand(): string
    {
        return DefaultCommand::class;
    }
    
    abstract public function bootDependencies(Application $app): void;
}
