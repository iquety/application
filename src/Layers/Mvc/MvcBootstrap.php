<?php

declare(strict_types=1);

namespace Freep\Application\Layers\Mvc;

use Freep\Application\Application;
use Freep\Application\Bootstrap;
use Freep\Application\Routing\Router;

abstract class MvcBootstrap implements Bootstrap
{
    abstract public function bootRoutes(Router $router): void;

    abstract public function bootDependencies(Application $app): void;
}
