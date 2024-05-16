<?php

declare(strict_types=1);

namespace Iquety\Application\AppEngine;

use Iquety\Injection\Container;

interface Bootstrap
{
    public function bootDependencies(Container $container): void;
}
