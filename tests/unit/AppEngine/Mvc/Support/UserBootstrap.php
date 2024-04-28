<?php

declare(strict_types=1);

namespace Tests\Unit\AppEngine\Mvc\Support;

use ArrayObject;
use Iquety\Application\Application;
use Iquety\Application\AppEngine\Mvc\MvcBootstrap;
use Iquety\Routing\Router;
use stdClass;
use Tests\AppEngine\Mvc\Support\Controllers\UserController;

class UserBootstrap extends MvcBootstrap
{
    public function bootRoutes(Router $router): void
    {
        $router->get('/user/:id')->usingAction(UserController::class . '::create');
        $router->post('/user/:id');
    }

    public function bootDependencies(Application $app): void
    {
        $app->addSingleton(ArrayObject::class, ArrayObject::class);
        $app->addSingleton(stdClass::class, stdClass::class);
    }
}