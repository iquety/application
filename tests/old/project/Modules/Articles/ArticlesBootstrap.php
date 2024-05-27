<?php

declare(strict_types=1);

namespace Modules\Articles;

use ArrayObject;
use Iquety\Application\Application;
use Iquety\Application\Bootstrap;
use Iquety\Routing\Router;
use stdClass;
use Tests\AppEngine\Mvc\Support\Controllers\UserController;

class ArticlesBootstrap implements Bootstrap
{
    public function bootRoutes(Router $router): void
    {
        $router->get('/article/:id')->usingAction(UserController::class . '::create');
        $router->post('/article/:id');
    }

    public function bootDependencies(Application $app): void
    {
        $app->addSingleton(ArrayObject::class, ArrayObject::class);

        $app->addSingleton(stdClass::class, stdClass::class);
    }
}
