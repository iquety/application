<?php

declare(strict_types=1);

namespace Tests;

use ArrayObject;
use Freep\Application\Application;
use Freep\Application\Bootstrap;
use Freep\Application\Routing\Router;
use stdClass;

class UserModuleBootstrap implements Bootstrap
{
    public function bootRoutes(Router $router): void
    {
        $router->get('/user/:id');
        $router->post('/user/:id');
    }

    public function bootDependencies(Application $app): void
    {
        $app->addSingleton(ArrayObject::class, ArrayObject::class);
        $app->addSingleton(stdClass::class, stdClass::class);
    }
}