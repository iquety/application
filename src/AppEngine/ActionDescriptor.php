<?php

declare(strict_types=1);

namespace Iquety\Application\AppEngine;

use Closure;

class ActionDescriptor
{
    public function __construct(
        private string $actionType,
        private string $bootstrapClass,
        private Closure|string $actionClass,
        private string $actionMethod = ''
    ) {
    }

    public function type(): string
    {
        return $this->actionType;
    }

    public function module(): string
    {
        return $this->bootstrapClass;
    }

    public function action(): Closure|string
    {
        if ($this->actionClass instanceof Closure) {
            return $this->actionClass;
        }

        return $this->actionClass . '::' . $this->actionMethod;
    }
}
