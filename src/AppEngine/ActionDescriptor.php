<?php

declare(strict_types=1);

namespace Iquety\Application\AppEngine;

class ActionDescriptor
{
    /** @param array<int,string|int|float> $params */
    public function __construct(
        private string $actionType,
        private string $bootstrapClass,
        private string $actionClass,
        private string $actionMethod
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

    public function action(): string
    {
        return $this->actionClass . '::' . $this->actionMethod;
    }
}
