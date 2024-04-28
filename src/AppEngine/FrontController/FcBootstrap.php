<?php

declare(strict_types=1);

namespace Iquety\Application\AppEngine\FrontController;

use Directory;
use Iquety\Application\Application;
use Iquety\Application\Bootstrap;
use Iquety\Application\Settings;

abstract class FcBootstrap implements Bootstrap
{
    public function commandsDirectory(): string
    {
        return 'Commands';
    }

    abstract public function bootDependencies(Application $app): void;
}
