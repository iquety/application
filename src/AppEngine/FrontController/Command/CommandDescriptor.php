<?php

declare(strict_types=1);

namespace Iquety\Application\AppEngine\FrontController\Command;

class CommandDescriptor
{
    /** @param array<int,string|int|float> $params */
    public function __construct(
        private string $bootstrapClass,
        private string $commandClass,
        private array $params
    ) {
    }

    public function module(): string
    {
        return $this->bootstrapClass;
    }

    public function action(): string
    {
        return $this->commandClass . '::execute';
    }

    /** @return array<int,string|int|float> */
    public function params(): array
    {
        return $this->params;
    }
}
