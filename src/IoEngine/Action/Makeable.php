<?php

declare(strict_types=1);

namespace Iquety\Application\IoEngine\Action;

use InvalidArgumentException;
use Iquety\Application\Application;

trait Makeable
{
    /** @param array<int,mixed> $arguments */
    public function make(...$arguments): mixed
    {
        return Application::instance()->make(...$arguments);
    }
}
