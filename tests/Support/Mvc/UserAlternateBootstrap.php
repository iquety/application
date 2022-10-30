<?php

declare(strict_types=1);

namespace Tests\Support\Mvc;

use ArrayObject;
use Iquety\Application\Application;
use Iquety\Application\Engine\Mvc\MvcBootstrap;
use Iquety\Routing\Router;
use stdClass;

class UserAlternateBootstrap extends MvcBootstrap
{
    public function bootRoutes(Router $router): void
    {
        $router->get('/editor/:id');
        $router->post('/editor/:id');
    }

    public function bootDependencies(Application $app): void
    {
        $app->addSingleton(ArrayObject::class, ArrayObject::class);
        $app->addSingleton(stdClass::class, stdClass::class);
    }
}
