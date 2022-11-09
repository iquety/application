<?php

declare(strict_types=1);

namespace Tests\Application\Support;

use ArrayObject;
use Iquety\Application\Application;
use Iquety\Application\AppEngine\FrontController\FcBootstrap;
use stdClass;

class FcBootMultiConcrete extends FcBootstrap
{
    public function bootDependencies(Application $app): void
    {
        $app->addSingleton(ArrayObject::class, ArrayObject::class);
        $app->addSingleton(stdClass::class, stdClass::class);
    }
}
