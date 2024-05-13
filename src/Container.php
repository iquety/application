<?php

declare(strict_types=1);

namespace Iquety\Application;

use Closure;
use Iquety\Injection\Container as InjectionContainer;

class Container extends InjectionContainer
{
    public function addFactory(string $identifier, Closure|string $factory): void
    {
        $this->registerDependency($identifier, $factory);
    }

    public function addSingleton(string $identifier, Closure|string $factory): void
    {
        $this->registerSingletonDependency($identifier, $factory);
    }
}

