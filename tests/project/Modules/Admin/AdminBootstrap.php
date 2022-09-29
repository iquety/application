<?php

declare(strict_types=1);

namespace Modules\Admin;

use Freep\Application\Application;
use Freep\Application\Bootstrap;
use Freep\Application\Route;
use Freep\Application\Router;

class AdminBootstrap implements Bootstrap
{
    public function bootRoutes(string $moduleIdentifier, Router $router): void
    {
        $route = new Route();
        $route->setPolice((object)[]);

        $router->addRoute($moduleIdentifier, $route);
    }

    public function bootDependencies(Application $app): void
    {
        $app->addFactory(Response::class, function () {
            return (object)[];
        });

        $app->addSingleton(ArrayObject::class, function () {
            return (object)[];
        });
    }
}
