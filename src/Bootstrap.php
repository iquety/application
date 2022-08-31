<?php

declare(strict_types=1);

namespace Freep\Application;

use Freep\Application\Routing\Router;

interface Bootstrap
{
    public function bootRoutes(Router $router): void;

    public function bootDependencies(Application $app): void;
}
