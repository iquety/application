<?php

declare(strict_types=1);

namespace Iquety\Application\Engine\Mvc;

use Iquety\Application\Application;
use Iquety\Application\Bootstrap;
use Iquety\Routing\Router;

abstract class MvcBootstrap implements Bootstrap
{
    abstract public function bootRoutes(Router $router): void;

    abstract public function bootDependencies(Application $app): void;
}
