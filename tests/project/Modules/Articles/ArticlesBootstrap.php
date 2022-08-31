<?php

declare(strict_types=1);

namespace Modules\Articles;

use Freep\Application\Application;
use Freep\Application\Bootstrap;
use Freep\Application\Route;
use Freep\Application\Router;

class ArticlesBootstrap implements Bootstrap
{
    public function bootRoutes(string $moduleIdentifier, Router $router): void
    {
        $router->addRoute($moduleIdentifier, new Route());
    }

    public function bootDependencies(Application $app): void
    {
        $app->addFactory(Response::class, function() { return (object)[]; });

        $app->addSingleton(ArrayObject::class, function() { return (object)[]; });
    }
}
