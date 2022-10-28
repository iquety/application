<?php

declare(strict_types=1);

namespace Tests\Support\Mvc;

use ArrayObject;
use Iquety\Application\Application;
use Iquety\Application\Layers\Mvc\MvcBootstrap;
use Iquety\Application\Routing\Router;
use stdClass;

class UserStringClosureActionBootstrap extends MvcBootstrap
{
    public function bootRoutes(Router $router): void
    {
        $router->get('/user/:id')->usingAction(fn() => 'closure naitis');
        $router->post('/user/:id');
    }

    public function bootDependencies(Application $app): void
    {
        $app->addSingleton(ArrayObject::class, ArrayObject::class);
        $app->addSingleton(stdClass::class, stdClass::class);
    }
}
