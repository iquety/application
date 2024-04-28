<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Support;

use ArrayObject;
use Iquety\Application\Application;
use Iquety\Application\AppEngine\Mvc\MvcBootstrap;
use Iquety\Routing\Router;
use stdClass;
use Tests\Application\Support\Controllers\Multi;

class MvcBootMultiConcrete extends MvcBootstrap
{
    public function bootRoutes(Router $router): void
    {
        $router->get('/multi/:id')->usingAction(Multi::class . '::create');
        $router->post('/multi/:id');
    }

    public function bootDependencies(Application $app): void
    {
        $app->addSingleton(ArrayObject::class, ArrayObject::class);
        $app->addSingleton(stdClass::class, stdClass::class);
    }
}
