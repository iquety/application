<?php

declare(strict_types=1);

namespace Tests\Support\Mvc;

use ArrayObject;
use Freep\Application\Application;
use Freep\Application\Layers\Mvc\MvcBootstrap;
use Freep\Application\Routing\Router;
use stdClass;

class UserNullClosureActionBootstrap extends MvcBootstrap
{
    public function bootRoutes(Router $router): void
    {
        $router->get('/user/:id')->usingAction(function () {
        });
        $router->post('/user/:id');
    }

    public function bootDependencies(Application $app): void
    {
        $app->addSingleton(ArrayObject::class, ArrayObject::class);
        $app->addSingleton(stdClass::class, stdClass::class);
    }
}
