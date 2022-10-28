<?php

declare(strict_types=1);

namespace Modules\Admin;

use ArrayObject;
use Iquety\Application\Application;
use Iquety\Application\Bootstrap;
use Iquety\Application\Routing\Router;
use stdClass;
use Tests\Support\UserController;

class AdminBootstrap implements Bootstrap
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
