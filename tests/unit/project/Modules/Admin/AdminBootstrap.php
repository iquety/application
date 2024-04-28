<?php

declare(strict_types=1);

namespace Modules\Admin;

use ArrayObject;
use Iquety\Application\AppEngine\FrontController\CommandHandler;
use Iquety\Application\AppEngine\FrontController\FcBootstrap;
use Iquety\Application\Application;
use Modules\Admin\Domain\User;
use Modules\Admin\Subscribers\UserSubscriber;
use stdClass;

class AdminBootstrap extends FcBootstrap
{
    public function bootDependencies(Application $app): void
    {
        $app->addSingleton(ArrayObject::class, ArrayObject::class);

        $app->addSingleton(stdClass::class, stdClass::class);

        $app->addSubscriber(User::label(), UserSubscriber::class);
    }
}
