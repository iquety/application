<?php

declare(strict_types=1);

namespace Freep\Application\Layers\FrontController;

use Freep\Application\Application;
use Freep\Application\Bootstrap;

abstract class FrontControllerBootstrap implements Bootstrap
{
    abstract public function loadCommandsFrom(string $path): void;

    abstract public function bootDependencies(Application $app): void;
}
