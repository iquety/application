<?php

declare(strict_types=1);

namespace Iquety\Application;

interface Bootstrap
{
    public function bootDependencies(Application $app): void;
}
