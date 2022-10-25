<?php

declare(strict_types=1);

namespace Freep\Application;

interface Bootstrap
{
    public function bootDependencies(Application $app): void;
}
