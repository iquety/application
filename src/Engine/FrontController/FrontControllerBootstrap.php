<?php

declare(strict_types=1);

namespace Iquety\Application\Engine\FrontController;

use Iquety\Application\Application;
use Iquety\Application\Bootstrap;

abstract class FrontControllerBootstrap implements Bootstrap
{
    abstract public function loadCommandsFrom(string $path): void;

    abstract public function bootDependencies(Application $app): void;
}
