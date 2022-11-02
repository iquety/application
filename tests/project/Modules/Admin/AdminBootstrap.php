<?php

declare(strict_types=1);

namespace Modules\Admin;

use ArrayObject;
use Iquety\Application\AppEngine\FrontController\CommandHandler;
use Iquety\Application\AppEngine\FrontController\FcBootstrap;
use Iquety\Application\Application;
use stdClass;

class AdminBootstrap extends FcBootstrap
{
    public function setupDirectories(CommandHandler $register): void
    {
    }

    public function bootDependencies(Application $app): void
    {
        $app->addSingleton(ArrayObject::class, ArrayObject::class);

        $app->addSingleton(stdClass::class, stdClass::class);
    }
}
